<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder\Customer;

use Klarna\Siwk\Model\Service;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class Generator
{

    /**
     * @var TypeResolver
     */
    private TypeResolver $typeResolver;
    /**
     * @var DateTime
     */
    private DateTime $dateTime;
    /**
     * @var Service
     */
    private Service $service;

    /**
     * Generate customer data
     *
     * @param TypeResolver $typeResolver
     * @param DateTime $dateTime
     * @param Service $service
     * @codeCoverageIgnore
     */
    public function __construct(TypeResolver $typeResolver, DateTime $dateTime, Service $service)
    {
        $this->typeResolver = $typeResolver;
        $this->dateTime = $dateTime;
        $this->service = $service;
    }

    /**
     * Getting back the basic data
     *
     * @param CartInterface $quote
     * @return array
     */
    public function getBasicData(CartInterface $quote): array
    {
        $result = [
            'type' => $this->typeResolver->getData($quote)
        ];

        $customerId = $quote->getCustomerId();
        if ($customerId !== null) {
            $tokenContainer = $this->service->getAccessToken((string) $customerId, $quote->getStore());
            if ($tokenContainer->hasAccessToken()) {
                $result['klarna_access_token'] = $tokenContainer->getAccessToken();
            }
        }

        return $result;
    }

    /**
     * Getting customer data with prefilled data
     *
     * @param CartInterface $magentoQuote
     * @return array
     */
    public function getWithPrefilledData(CartInterface $magentoQuote): array
    {
        $customer = $this->getBasicData($magentoQuote);

        if (!$magentoQuote->getCustomerIsGuest() && $magentoQuote->getCustomerDob()) {
            $customer['date_of_birth'] = $this->dateTime->date('Y-m-d', $magentoQuote->getCustomerDob());
        }

        return $customer;
    }
}
