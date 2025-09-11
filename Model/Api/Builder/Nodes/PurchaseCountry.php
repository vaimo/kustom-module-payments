<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder\Nodes;

use Klarna\Base\Model\Quote\Address\Country;
use Klarna\Kp\Model\Api\Request\Builder;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class PurchaseCountry
{
    /**
     * @var Country
     */
    private Country $country;

    /**
     * @param Country $country
     * @codeCoverageIgnore
     */
    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    /**
     * Adding the purchase country to the request
     *
     * @param Builder $requestBuilder
     * @param CartInterface $magentoQuote
     */
    public function addToRequest(Builder $requestBuilder, CartInterface $magentoQuote): void
    {
        $country = $this->country->getCountry($magentoQuote);
        $requestBuilder->setPurchaseCountry($country);
    }
}
