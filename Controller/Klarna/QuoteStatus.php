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
use Klarna\Base\Model\Responder\Result;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Logger\Api\LoggerInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Checkout\Model\Session;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @api
 */
class QuoteStatus implements HttpPostActionInterface, RequestHandlerInterface
{
    use RequestTrait;

    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $magentoQuoteRepository;
    /**
     * @var Result
     */
    private Result $result;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var Session
     */
    private Session $checkoutSession;
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @param RequestInterface $request
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param CartRepositoryInterface $magentoQuoteRepository
     * @param Result $result
     * @param LoggerInterface $logger
     * @param Session $checkoutSession
     * @param OrderRepositoryInterface $orderRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        RequestInterface $request,
        QuoteRepositoryInterface $klarnaQuoteRepository,
        CartRepositoryInterface $magentoQuoteRepository,
        Result $result,
        LoggerInterface $logger,
        Session $checkoutSession,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->request = $request;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->magentoQuoteRepository = $magentoQuoteRepository;
        $this->result = $result;
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Getting back information from the quote, if the quote was active then we will be allowed to place the order from
     * the website frontend and if it was not active it means the quote's respective order has already been placed,
     * and we don't need to place an order from the frontend
     *
     * @return Json
     */
    public function execute()
    {
        $response = ['is_active' => 1];
        $response['status'] = $status = 400;
        $rawParameter = json_decode($this->request->getContent(), true);

        if (!isset($rawParameter['authorization_token'])) {
            $response['message'] = $message = 'No authorization_token parameter found in the request body.';
            $this->logger->warning($message);
            return $this->result->getJsonResult($status, $response);
        }

        $authorizationToken = $rawParameter['authorization_token'];
        if (empty($authorizationToken)) {
            $response['message'] = $message = 'Provided authorization_token field is empty.';
            $this->logger->notice($message);
            return $this->result->getJsonResult($status, $response);
        }

        try {
            $maxTime = 25;
            $sleepDuration = 0;
            do {
                // phpcs:ignore Magento2.Functions.DiscouragedFunction
                sleep(2);
                $sleepDuration += 2;
                $klarnaQuote = $this->klarnaQuoteRepository->getByAuthorizationToken($authorizationToken);
                $klarnaOrderId = $klarnaQuote->getOrderId();
                $magentoOrder = null;
                if (!empty($klarnaOrderId)) {
                    $magentoOrder = $this->orderRepository->get((int) $klarnaOrderId);
                }
            } while ($klarnaQuote->isAuthCallbackInProgress() && $sleepDuration < $maxTime);
        } catch (NoSuchEntityException $e) {
            $response['status'] = $status = 200;
            $response['message'] = sprintf(
                'Could not check the authorization callback workflow status. Reason: %s',
                $e->getMessage()
            );
            $this->logger->critical($e);
            return $this->result->getJsonResult($status, $response);
        }
        $klarnaQuoteId = (int) $klarnaQuote->getQuoteId();
        $magentoQuote = $this->magentoQuoteRepository->get($klarnaQuoteId);
        $response['is_active'] = (int) (
            $magentoQuote->getIsActive() &&
            $klarnaQuote->isAuthCallbackFailedOrNotStarted()
        );
        $response['message'] = sprintf('The quote is %s.', $response['is_active'] ? 'active' : 'inactive');
        $response['status'] = $status = 200;

        $this->checkoutSession->setLastQuoteId($klarnaQuoteId);
        $this->checkoutSession->setLastSuccessQuoteId($klarnaQuoteId);
        if (!empty($magentoOrder)) {
            $this->checkoutSession->setLastOrderId($magentoOrder->getId());
            $this->checkoutSession->setLastRealOrderId($magentoOrder->getIncrementId());
            $this->checkoutSession->setLastOrderStatus($magentoOrder->getStatus());
        }

        return $this->result->getJsonResult($status, $response);
    }
}
