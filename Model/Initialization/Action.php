<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Initialization;

use Klarna\Base\Exception as KlarnaException;
use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\QuoteFactory as KlarnaQuoteFactory;
use Klarna\Logger\Api\LoggerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use Klarna\PluginsApi\Model\Update\Validator as PluginsApiValidator;

/**
 * @internal
 */
class Action
{
    /**
     * @var Validator
     */
    private Validator $validator;
    /**
     * @var KlarnaQuoteHandler
     */
    private KlarnaQuoteHandler $klarnaQuoteHandler;
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;
    /**
     * @var KlarnaQuoteFactory
     */
    private KlarnaQuoteFactory $klarnaQuoteFactory;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var PluginsApiValidator
     */
    private PluginsApiValidator $pluginsApiValidator;

    /**
     * @param Validator $validator
     * @param KlarnaQuoteHandler $klarnaQuoteHandler
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param KlarnaQuoteFactory $klarnaQuoteFactory
     * @param LoggerInterface $logger
     * @param PluginsApiValidator $pluginsApiValidator
     * @codeCoverageIgnore
     */
    public function __construct(
        Validator                $validator,
        KlarnaQuoteHandler       $klarnaQuoteHandler,
        QuoteRepositoryInterface $klarnaQuoteRepository,
        KlarnaQuoteFactory       $klarnaQuoteFactory,
        LoggerInterface          $logger,
        PluginsApiValidator      $pluginsApiValidator
    ) {
        $this->validator = $validator;
        $this->klarnaQuoteHandler = $klarnaQuoteHandler;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->klarnaQuoteFactory = $klarnaQuoteFactory;
        $this->logger = $logger;
        $this->pluginsApiValidator = $pluginsApiValidator;
    }

    /**
     * Sending the request and returning the response object.
     *
     * If the method was called on a previous step then the result of the last method call will be returned.
     * If a request still ahs to be sent the boolean parameter has to be set to true.
     *
     * @param CartInterface $magentoQuote
     * @param array $options
     * @return QuoteInterface
     * @throws KlarnaException
     * @throws KlarnaApiException
     */
    public function sendRequest(CartInterface $magentoQuote, array $options = []): QuoteInterface
    {
        $klarnaQuote = $this->fetchKlarnaQuote($magentoQuote);

        $store = $magentoQuote->getStore();
        if ($this->pluginsApiValidator->isPspMerchantByStore($store)) {
            return $klarnaQuote;
        }

        try {
            if ($this->isAKecSession($klarnaQuote)) {
                return $klarnaQuote;
            }

            $disableAuthorizationCallback = $options['disableAuthorizationCallback'] ?? false;

            $klarnaQuote = $this->shouldUpdateSession($klarnaQuote, $magentoQuote) ?
                $this->klarnaQuoteHandler->updateKlarnaQuote($magentoQuote, $klarnaQuote) :
                $this->klarnaQuoteHandler->createKlarnaQuote($magentoQuote, $disableAuthorizationCallback);
        } catch (KlarnaException $exception) {
            $this->logger->error($exception->getMessage());
            $this->logger->critical($exception);
        }

        return $klarnaQuote;
    }

    /**
     * Fetching the Klarna quote
     *
     * @param CartInterface $magentoQuote
     * @return QuoteInterface
     */
    private function fetchKlarnaQuote(CartInterface $magentoQuote): QuoteInterface
    {
        try {
            $klarnaQuote = $this->klarnaQuoteRepository->getActiveByQuote($magentoQuote);
        } catch (NoSuchEntityException $e) {
            $klarnaQuote = $this->klarnaQuoteFactory->create();
        }

        return $klarnaQuote;
    }

    /**
     * Check if the session is a KEC session
     *
     * @param QuoteInterface $klarnaQuote
     * @return bool
     */
    private function isAKecSession(QuoteInterface $klarnaQuote): bool
    {
        return $this->validator->isKlarnaSessionRunning($klarnaQuote) &&
            $klarnaQuote->isKecSession();
    }

    /**
     * Check if the session should be updated or not
     *
     * @param QuoteInterface $klarnaQuote
     * @param CartInterface $magentoQuote
     * @return bool
     */
    private function shouldUpdateSession(
        QuoteInterface $klarnaQuote,
        CartInterface $magentoQuote
    ): bool {
        if (!$this->validator->isKlarnaSessionRunning($klarnaQuote)) {
            return false;
        }

        if ($this->validator->isCustomerTypeChanged($klarnaQuote, $magentoQuote)) {
            $this->klarnaQuoteHandler->markInactive($klarnaQuote);

            return false;
        }

        return true;
    }
}
