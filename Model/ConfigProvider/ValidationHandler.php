<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\ConfigProvider;

use Klarna\Kp\Model\Configuration\ApiValidation;
use Magento\Framework\Phrase;
use Magento\Quote\Model\Quote;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @internal
 */
class ValidationHandler
{
    /**
     * @var ApiValidation
     */
    private ApiValidation $apiValidation;

    /**
     * @param ApiValidation $apiValidation
     * @codeCoverageIgnore
     */
    public function __construct(ApiValidation $apiValidation)
    {
        $this->apiValidation = $apiValidation;
    }

    /**
     * Returns true if KP is enabled
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isKpEnabled(StoreInterface $store): bool
    {
        return $this->apiValidation->isKpEnabled($store);
    }

    /**
     * Validate if KP is allowed to be shown
     *
     * @param Quote $quote
     * @return bool
     */
    public function validateApi(Quote $quote): bool
    {
        $this->apiValidation->clearFailedValidationHistory();
        return $this->apiValidation->sendApiRequestAllowed($quote);
    }

    /**
     * In case of failure validation of KP this method will return the validation message
     *
     * @return Phrase
     */
    public function getValidationMessage(): Phrase
    {
        return __('Klarna Payments will not show up. Reason: ' .
            implode(', ', $this->apiValidation->getFailedValidationHistory()));
    }
}
