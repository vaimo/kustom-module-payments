<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder\Nodes;

use Klarna\Kp\Model\Api\Builder\Nodes\PurchaseCountry;
use Klarna\Kp\Model\Api\Request\Builder;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\Store;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\PurchaseCountry
 */
class PurchaseCountryTest extends TestCase
{
    /**
     * @var PurchaseCountry
     */
    private PurchaseCountry $model;
    /**
     * @var Builder
     */
    private Builder $requestBuilder;
    /**
     * @var Quote
     */
    private Quote $quote;

    public function testAddToRequestAddingTheCountryValue(): void
    {
        $expected = 'DE';
        $this->dependencyMocks['country']->method('getCountry')
            ->willReturn($expected);

        $this->requestBuilder->expects(static::once())
            ->method('setPurchaseCountry')
            ->with($expected);
        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(PurchaseCountry::class);

        $this->requestBuilder = $this->mockFactory->create(Builder::class);
        $this->quote = $this->mockFactory->create(Quote::class);
    }
}
