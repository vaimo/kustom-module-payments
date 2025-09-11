<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Initialization\Payload;

use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Kp\Model\Initialization\Payload\QuoteFiller;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Data\Customer;
use Magento\Framework\DataObject;
use Magento\OfflineShipping\Model\Carrier\Flatrate;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\Store;

class QuoteFillerTest extends TestCase
{
    /**
     * @var QuoteFiller
     */
    private QuoteFiller $quoteFiller;
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var Address
     */
    private Address $shippingAddress;
    /**
     * Store
     */
    private Store $store;

    public function testFillExistingMagentoQuoteQuoteIsVirtual(): void
    {
        $this->quote->expects(static::never())
            ->method('collectTotals');
        $this->quote->method('isVirtual')
            ->willReturn(true);
        $this->quoteFiller->fillExistingMagentoQuote($this->quote, []);
    }

    public function testFillExistingMagentoQuoteCountryIdWillBeSet(): void
    {
        $this->shippingAddress->expects(static::once())
            ->method('setCountryID')
            ->with('DE');
        $this->quote->method('isVirtual')
            ->willReturn(false);

        $parameter = [
            'country_id' => 'DE',
            'use_existing_quote' => true
        ];
        $this->quoteFiller->fillExistingMagentoQuote($this->quote, $parameter);
    }

    public function testFillExistingMagentoQuoteShippingMethodWillBeSet(): void
    {
        $this->dependencyMocks['quoteMethodHandler']->expects(static::once())
            ->method('setShippingMethod')
            ->with($this->quote, 'my_carrier_my_method');
        $this->quote->method('isVirtual')
            ->willReturn(false);

        $parameter = [
            'shipping_method' => 'my_method',
            'shipping_carrier_code' => 'my_carrier',
            'use_existing_quote' => true
        ];
        $this->quoteFiller->fillExistingMagentoQuote($this->quote, $parameter);
    }

    public function testFillExistingMagentoQuoteIsVirtualReturnsOriginalQuote(): void
    {
        $this->quote->method('isVirtual')
            ->willReturn(true);
        static::assertSame($this->quote, $this->quoteFiller->fillExistingMagentoQuote($this->quote, []));
    }

    public function testFillEmptyMagentoQuoteAddingProduct(): void
    {
        $parameter =
            [
                'product' => '1'
            ];

        $product = $this->mockFactory->create(Product::class);
        $this->dependencyMocks['productRepository']->method('getById')
            ->willReturn($product);
        $this->quote->expects(static::once())
            ->method('addProduct');
        $this->quote->method('isVirtual')
            ->willReturn(true);
        $this->quoteFiller->fillEmptyMagentoQuote($this->quote, $parameter);
    }

    public function testFillEmptyMagentoQuoteAddingCurrency(): void
    {
        $this->quote->expects(static::once())
            ->method('setBaseCurrencyCode')
            ->with('EUR');
        $this->quote->expects(static::once())
            ->method('setGlobalCurrencyCode')
            ->with('EUR');
        $this->quote->expects(static::once())
            ->method('setQuoteCurrencyCode')
            ->with('EUR');
        $this->quote->expects(static::once())
            ->method('setStoreCurrencyCode')
            ->with('EUR');

        $parameter =
            [
                'product' => '1',
            ];
        $product = $this->mockFactory->create(Product::class);
        $this->dependencyMocks['productRepository']->method('getById')
            ->willReturn($product);

        $this->quote->method('isVirtual')
            ->willReturn(true);
        $this->quoteFiller->fillEmptyMagentoQuote($this->quote, $parameter);
    }

    public function testFillEmptyMagentoQuoteAddingStore(): void
    {
        $this->quote->expects(static::once())
            ->method('setStore')
            ->with($this->store);

        $parameter =
            [
                'product' => '1',
            ];
        $product = $this->mockFactory->create(Product::class);
        $this->dependencyMocks['productRepository']->method('getById')
            ->willReturn($product);

        $this->quote->method('isVirtual')
            ->willReturn(true);
        $this->quoteFiller->fillEmptyMagentoQuote($this->quote, $parameter);
    }

    public function testFillEmptyMagentoQuoteQuoteIsNotVirtual(): void
    {
        $flatrate = $this->mockFactory->create(
            Flatrate::class,
            [],
            [
                'getCode'
            ]
        );
        $flatrate->method('getCode')
            ->willReturn('flatrate_flatrate');
        $this->dependencyMocks['directoryHelper']->method('getDefaultCountry')
            ->willReturn('DE');
        $this->shippingAddress->method('getGroupedAllShippingRates')
            ->willReturn(
                [
                    'carrier_method' => [
                        $flatrate
                    ]
                ]
            );

        $this->dependencyMocks['quoteMethodHandler']->expects(static::once())
            ->method('setShippingMethod')
            ->with($this->quote, 'flatrate_flatrate');

        $parameter =
            [
                'product' => '1',
            ];
        $product = $this->mockFactory->create(Product::class);
        $this->dependencyMocks['productRepository']->method('getById')
            ->willReturn($product);

        $this->quote->method('isVirtual')
            ->willReturn(false);
        $this->quoteFiller->fillEmptyMagentoQuote($this->quote, $parameter);
    }

    public function testFillEmptyMagentoQuoteAssigningCustomerToQuote(): void
    {
        $customer = $this->mockFactory->create(Customer::class);
        $this->dependencyMocks['customerSession']->method('getCustomerData')
            ->willReturn($customer);
        $this->dependencyMocks['customerSession']->method('isLoggedIn')
            ->willReturn(true);
        $this->quote->expects(static::once())
            ->method('assignCustomer')
            ->with($customer);

        $parameter =
            [
                'product' => '1',
            ];
        $product = $this->mockFactory->create(Product::class);
        $this->dependencyMocks['productRepository']->method('getById')
            ->willReturn($product);

        $this->quote->method('isVirtual')
            ->willReturn(true);
        $this->quoteFiller->fillEmptyMagentoQuote($this->quote, $parameter);
    }

    protected function setUp(): void
    {
        $this->quoteFiller = parent::setUpMocks(QuoteFiller::class);

        $this->shippingAddress = $this->mockFactory->create(Address::class);
        $this->quote = $this->mockFactory->create(
            Quote::class,
            [
                'getShippingAddress',
                'isVirtual',
                'addProduct',
                'collectTotals',
                'setStore',
                'assignCustomer',
                'getStore',
                'getId',
                'removeAllItems',
                'getBillingAddress'
            ],
            [
                'setBaseCurrencyCode',
                'setGlobalCurrencyCode',
                'setQuoteCurrencyCode',
                'setStoreCurrencyCode',
                'setTotalsCollectedFlag',
            ]
        );
        $this->quote->method('getShippingAddress')
            ->willReturn($this->shippingAddress);
        $this->quote->method('getId')
            ->willReturn('1');

        $this->store = $this->mockFactory->create(Store::class);
        $this->store->method('getCurrentCurrencyCode')
            ->willReturn('EUR');
        $this->dependencyMocks['storeManager']->method('getStore')
            ->willReturn($this->store);
        $this->quote->method('getStore')
            ->willReturn($this->store);

        $address = $this->mockFactory->create(Address::class);
        $this->quote->method('getBillingAddress')
            ->willReturn($address);

        $dataObject = $this->mockFactory->create(DataObject::class);
        $this->dependencyMocks['dataObjectFactory']->method('create')
            ->willReturn($dataObject);
    }
}