<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Configuration;

use Klarna\AdminSettings\Model\Configurations\General;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Quote\Api\Data\CartInterface;
use Klarna\AdminSettings\Model\Configurations\Kp;

/**
 * @internal
 */
class ApiValidation
{
    /**
     * @var array
     */
    private array $failedValidationHistory = [];
    /**
     * @var General
     */
    private General $generalConfig;
    /**
     * @var Kp
     */
    private Kp $paymentConfig;

    /**
     * @param General $generalConfig
     * @param Kp $paymentConfig
     * @codeCoverageIgnore
     */
    public function __construct(General $generalConfig, Kp $paymentConfig)
    {
        $this->generalConfig = $generalConfig;
        $this->paymentConfig = $paymentConfig;
    }

    /**
     * Returns true if KP is enabled
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isKpEnabled(StoreInterface $store): bool
    {
        $result = $this->paymentConfig->isEnabled($store);
        if (!$result) {
            $this->failedValidationHistory[] = 'Klarna Payments in not enabled';
        }

        return $result;
    }

    /**
     * Returns true if the KP api request is allowed to be sent
     *
     * @param CartInterface $quote
     * @return bool
     */
    public function sendApiRequestAllowed(CartInterface $quote): bool
    {
        $store = $quote->getStore();
        if (!$this->isKpEnabled($store)) {
            return false;
        }

        $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        $quoteCountry = $address->getCountryId();

        if (!$quoteCountry) {
            return true;
        }

        $result = $this->generalConfig->isCountryAllowed($store, $quoteCountry);
        if (!$result) {
            $this->failedValidationHistory[] =
                'Klarna Payments is not allowed to be shown for quote id: ' . $quote->getId();
        }

        return $result;
    }

    /**
     * Getting back the failed validation history
     *
     * @return array
     */
    public function getFailedValidationHistory(): array
    {
        return $this->failedValidationHistory;
    }

    /**
     * Clearing the failed validation history
     */
    public function clearFailedValidationHistory():void
    {
        $this->failedValidationHistory = [];
    }
}
