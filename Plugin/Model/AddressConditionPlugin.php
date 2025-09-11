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
use Klarna\Kp\Model\PaymentMethods\GenericPaymentKey;
use Klarna\Kp\Model\PaymentMethods\Session as KlarnaSession;
use Magento\SalesRule\Model\Rule\Condition\Address;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * @internal
 */
class AddressConditionPlugin
{
    /**
     * @var GenericPaymentKey
     */
    private GenericPaymentKey $genericPaymentKey;

    /**
     * @param GenericPaymentKey $genericPaymentKey
     * @codeCoverageIgnore
     */
    public function __construct(GenericPaymentKey $genericPaymentKey)
    {
        $this->genericPaymentKey = $genericPaymentKey;
    }

    /**
     * Checks value and replaces detailed payment method names with generic kp key
     *
     * @param Address $address
     * @param mixed $validatedValue
     * @return mixed
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function beforeValidateAttribute(Address $address, $validatedValue)
    {
        if ($validatedValue === null) {
            return $validatedValue;
        }
        if (!is_array($validatedValue)) {
            $validatedValue = (string) $validatedValue;
        }
        return $this->genericPaymentKey->getGenericKpKey($validatedValue);
    }
}
