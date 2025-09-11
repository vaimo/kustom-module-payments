<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Plugin\Model;

use Klarna\Kp\Model\Configuration\ApiValidation;
use Klarna\Kp\Model\Payment\Kp;
use Magento\AdvancedSalesRule\Model\Rule\Condition\FilterTextGenerator\Address\PaymentMethod;
use Klarna\Kp\Model\PaymentMethods\Session as KlarnaSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Klarna\Kp\Model\QuoteRepository;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class PaymentMethodPlugin
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
     * @var QuoteRepository
     */
    private QuoteRepository $klarnaQuoteRepository;
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;
    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;

    /**
     * @param KlarnaSession $klarnaSession
     * @param ApiValidation $apiValidation
     * @param QuoteRepository $klarnaQuoteRepository
     * @param StoreManagerInterface $storeManager
     * @param CheckoutSession $checkoutSession
     * @codeCoverageIgnore
     */
    public function __construct(
        KlarnaSession $klarnaSession,
        ApiValidation $apiValidation,
        QuoteRepository $klarnaQuoteRepository,
        StoreManagerInterface $storeManager,
        CheckoutSession $checkoutSession
    ) {
        $this->klarnaSession = $klarnaSession;
        $this->apiValidation = $apiValidation;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Replaces detailed payment method names with generic kp key
     *
     * @param PaymentMethod $method
     * @param array         $result
     * @return array
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function afterGenerateFilterText($method, array $result): array
    {
        $magentoQuoteId = $this->checkoutSession->getQuoteId();
        if (empty($magentoQuoteId)) {
            return $result;
        }

        if (!$this->klarnaQuoteRepository->existsEntryByQuoteId((string) $magentoQuoteId)) {
            return $result;
        }

        $store = $this->storeManager->getStore();
        if (!$this->apiValidation->isKpEnabled($store)) {
            return $result;
        }

        $handledFilterTextParts = [];
        foreach ($result as $filterTextPart) {
            if ($this->isPaymentMethod($filterTextPart)) {
                $filterTextPart = $this->replacePaymentMethod($filterTextPart);
            }
            $handledFilterTextParts[] = $filterTextPart;
        }
        return $handledFilterTextParts;
    }

    /**
     * Checks if input is a payment method
     *
     * @param string $input
     * @return bool
     */
    private function isPaymentMethod(string $input): bool
    {
        return strpos($input, 'quote_address:payment_method') === 0;
    }

    /**
     * Replaces payment methods saved in klarna quote with the kp key
     *
     * @param string $input
     * @return string
     */
    private function replacePaymentMethod(string $input): string
    {
        return str_replace(
            $this->klarnaSession->getPaymentMethods(),
            Kp::METHOD_CODE,
            $input
        );
    }
}
