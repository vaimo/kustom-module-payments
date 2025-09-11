<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\PaymentMethods;

use Klarna\Kp\Model\Configuration\ApiValidation;
use Klarna\Kp\Model\Payment\Kp;
use Klarna\Kp\Model\PaymentMethods\Session as KlarnaSession;
use Klarna\Kp\Model\QuoteRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class GenericPaymentKey
{
    /**
     * @var KlarnaSession
     */
    private KlarnaSession $klarnaSession;
    /**
     * @var ApiValidation
     */
    private ApiValidation $apiValidation;
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $magentoQuoteRepository;
    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;
    /**
     * @var QuoteRepository
     */
    private QuoteRepository $quoteRepository;
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param KlarnaSession $klarnaSession
     * @param ApiValidation $apiValidation
     * @param CartRepositoryInterface $magentoQuoteRepository
     * @param CheckoutSession $checkoutSession
     * @param QuoteRepository $quoteRepository
     * @param StoreManagerInterface $storeManager
     * @codeCoverageIgnore
     */
    public function __construct(
        KlarnaSession $klarnaSession,
        ApiValidation $apiValidation,
        CartRepositoryInterface $magentoQuoteRepository,
        CheckoutSession $checkoutSession,
        QuoteRepository $quoteRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->klarnaSession = $klarnaSession;
        $this->apiValidation = $apiValidation;
        $this->magentoQuoteRepository = $magentoQuoteRepository;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Replaces detailed payment method names with generic kp key
     *
     * @param mixed $validatedValue
     * @return mixed
     */
    public function getGenericKpKey($validatedValue)
    {
        $magentoQuoteId = $this->checkoutSession->getQuoteId();
        if (empty($magentoQuoteId)) {
            return $validatedValue;
        }

        if (!$this->quoteRepository->existsEntryByQuoteId($magentoQuoteId)) {
            return $validatedValue;
        }

        $store = $this->storeManager->getStore();
        if ($this->apiValidation->isKpEnabled($store)) {
            $validatedTrimmed = str_replace('klarna_', '', $validatedValue);
            $paymentMethods = $this->klarnaSession->getPaymentMethodInformation();
            foreach ($paymentMethods as $paymentMethod) {
                if (in_array($validatedTrimmed, $paymentMethod)) {
                    return Kp::METHOD_CODE;
                }
            }
        }

        return $validatedValue;
    }
}
