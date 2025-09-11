<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

declare(strict_types=1);

namespace Klarna\Kp\Controller\Klarna;

use Klarna\Base\Api\RequestHandlerInterface;
use Klarna\Base\Controller\RequestTrait;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Logger\Model\Logger;
use Magento\Checkout\Model\Session;
use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\Webapi\Exception;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @api
 */
class Cookie implements HttpGetActionInterface, RequestHandlerInterface
{
    use RequestTrait;

    /**
     * @var Session
     */
    private $checkoutSession;
    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var DefaultConfigProvider
     */
    private $defaultConfigProvider;
    /**
     * @var RedirectFactory
     */
    private RedirectFactory $redirectFactory;
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;
    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @param Session $session
     * @param UrlInterface $urlBuilder
     * @param DefaultConfigProvider $defaultConfigProvider
     * @param RedirectFactory $redirectFactory
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param Logger $logger
     * @param RequestInterface $request
     * @codeCoverageIgnore
     */
    public function __construct(
        Session $session,
        UrlInterface $urlBuilder,
        DefaultConfigProvider $defaultConfigProvider,
        RedirectFactory $redirectFactory,
        QuoteRepositoryInterface $klarnaQuoteRepository,
        Logger $logger,
        RequestInterface $request
    ) {
        $this->checkoutSession = $session;
        $this->urlBuilder = $urlBuilder;
        $this->defaultConfigProvider = $defaultConfigProvider;
        $this->redirectFactory = $redirectFactory;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->logger = $logger;
        $this->request = $request;
    }

    /**
     * Redirecting the customer to a url to set the cookie.
     *
     * @return ResponseInterface|Json|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        return $this->redirectFactory->create()->setPath($this->getRedirectUrl());
    }

    /**
     * Retrieve the redirect url, that was set to the checkout session during the authorize
     *
     * @return string
     */
    private function getRedirectUrl(): string
    {
        $quoteId = $this->checkoutSession->getLastQuoteId();
        $errorMessage = 'No Klarna redirect URL could be used because';
        if ($quoteId === null) {
            $this->logger->warning(
                "$errorMessage no final Magento quote ID is added to the checkout session"
            );
            return $this->getSuccessPageUrl();
        }

        try {
            $klarnaQuote = $this->klarnaQuoteRepository->getActiveByQuoteId((string) $quoteId);
        } catch (NoSuchEntityException $e) {
            $this->logger->warning(
                "$errorMessage there is no active Klarna quote for the Magento quote ID: $quoteId"
            );
            $this->logger->critical($e);
            return $this->getSuccessPageUrl();
        }

        try {
            $magentoOrder = null;
            $klarnaOrderId = $klarnaQuote->getOrderId();
            if (empty($klarnaOrderId)) {
                $this->logger->warning(
                    "$errorMessage no order ID has been set in Klarna quote"
                );
                return $this->getSuccessPageUrl();
            }

            $orderRepository = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Sales\Api\OrderRepositoryInterface::class);
            $magentoOrder = $orderRepository->get((int) $klarnaOrderId);

            if ($magentoOrder === null) {
                $this->logger->warning(
                    "$errorMessage no Magento order has been found for the order ID: $klarnaOrderId"
                );
                return $this->getSuccessPageUrl();
            }
            $this->checkoutSession->setLastOrderId($magentoOrder->getId());
            $this->checkoutSession->setLastRealOrderId($magentoOrder->getIncrementId());
            $this->checkoutSession->setLastOrderStatus($magentoOrder->getStatus());
        } catch (\Exception $e) {
            $this->logger->warning(
                "$errorMessage {$e->getMessage()}"
            );
            $this->logger->critical($e);
            return $this->getSuccessPageUrl();
        }

        $redirectUrl = $klarnaQuote->getRedirectUrl();
        $this->checkoutSession->setLastSuccessQuoteId($klarnaQuote->getQuoteId());
        if (!$redirectUrl) {
            $this->logger->warning(
                "$errorMessage there is no redirect URL set for the Klarna quote ID: {$klarnaQuote->getId()}"
            );
            return $this->getSuccessPageUrl();
        }
        return $redirectUrl;
    }

    /**
     * Getting back the success page url
     *
     * @return string
     */
    private function getSuccessPageUrl(): string
    {
        return $this->urlBuilder->getUrl($this->defaultConfigProvider->getDefaultSuccessPageUrl());
    }
}
