<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder\Nodes\Addresses;

use Klarna\Kp\Model\Api\Builder\Nodes\Addresses\Builder;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Kp\Model\Api\Request\Builder as RequestBuilder;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\Store;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\Addresses\Builder
 */
class BuilderTest extends TestCase
{
    /**
     * @var Builder
     */
    private Builder $model;
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var RequestBuilder
     */
    private RequestBuilder $requestBuilder;

    public function testAddToRequestQuoteIsVirtualAndJustBillingAddressIsSetForTheRequest(): void
    {
        $this->quote->method('getIsVirtual')
            ->willReturn(true);
        $this->requestBuilder->expects(static::once())
            ->method('setBillingAddress');
        $this->requestBuilder->expects(static::never())
            ->method('setShippingAddress');

        $this->dependencyMocks['mapper']->method('getKlarnaDataFromAddress')
            ->willReturn([]);

        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    public function testAddToRequestQuoteIsNotVirtualAndBothAddressesAreSetForTheRequest(): void
    {
        $this->quote->method('getIsVirtual')
            ->willReturn(false);
        $this->requestBuilder->expects(static::once())
            ->method('setBillingAddress');
        $this->requestBuilder->expects(static::once())
            ->method('setShippingAddress');

        $this->dependencyMocks['mapper']->method('getKlarnaDataFromAddress')
            ->willReturn([]);

        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(Builder::class);

        $store = $this->mockFactory->create(Store::class);
        $address = $this->mockFactory->create(Address::class);
        $this->quote = $this->mockFactory->create(
            Quote::class,
            [
                'getIsVirtual',
                'getBillingAddress',
                'getShippingAddress',
                'getStore'
            ]
        );
        $this->quote->method('getStore')
            ->willReturn($store);
        $this->quote->method('getBillingAddress')
            ->willReturn($address);
        $this->quote->method('getShippingAddress')
            ->willReturn($address);
        $this->requestBuilder = $this->mockFactory->create(RequestBuilder::class);
    }
}
