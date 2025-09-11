<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\PaymentMethods;

use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Logger\Api\LoggerInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Payment;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @internal
 */
class AdditionalInformationUpdater
{
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param LoggerInterface          $logger
     * @codeCoverageIgnore
     */
    public function __construct(QuoteRepositoryInterface $klarnaQuoteRepository, LoggerInterface $logger)
    {
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->logger = $logger;
    }

    /**
     * Updating the additional information based on the data given from the input parameters.
     *
     * @param DataObject $additionalData
     * @param Payment $payment
     */
    public function updateByInput(DataObject $additionalData, Payment $payment): void
    {
        $magentoQuote = $payment->getQuote();

        try {
            $klarnaQuote = $this->klarnaQuoteRepository->getActiveByQuote($magentoQuote);

            $payment->setAdditionalInformation('method_title', $additionalData->getData('method_title'));
            $payment->setAdditionalInformation('logo', $additionalData->getData('logo'));
            $payment->setAdditionalInformation('method_code', $payment->getMethodInstance()->getCode());
            $payment->setAdditionalInformation('klarna_order_id', $klarnaQuote->getSessionId());
            $this->klarnaQuoteRepository->save($klarnaQuote);
        } catch (LocalizedException $exception) {
            $this->logger->critical($exception);
        }
    }
}
