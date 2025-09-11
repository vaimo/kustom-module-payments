<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Integration\Model\Api\Builder;

use Klarna\Base\Model\Api\Exception as KlarnaApiException;
use Klarna\Base\Test\Integration\Helper\RequestBuilderTestCase;

/**
 * @internal
 */
class RequestTest extends RequestBuilderTestCase
{
    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/customer_us_with_address_same_billing_shipping.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGeneratePlaceOrderRequestLoggedInCustomerWithBothDefaultAddressesReturnsRequestWithValidAddressesOnDefaultLevel(): void // phpcs:ignore
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getPlaceOrderRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );

        $requestBillingAddress = $request['billing_address'];
        $requestShippingAddress = $request['shipping_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertSame($requestBillingAddress, $requestShippingAddress);
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/customer_us_with_address_same_billing_shipping.php
     *
     * @magentoConfigFixture current_store general/country/default US
     * @magentoConfigFixture current_store general/store_information/country_id US
     * @magentoConfigFixture current_store general/store_information/region_id 82
     * @magentoConfigFixture current_store tax/defaults/country US
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/discount_tax 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id US
     * @magentoConfigFixture current_store shipping/origin/region_id 1
     * @magentoConfigFixture current_store tax/display/shipping 1
     * @magentoConfigFixture current_store tax/display/type 1
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 1
     * @magentoConfigFixture default/currency/options/base USD
     * @magentoConfigFixture current_store currency/options/default USD
     * @magentoConfigFixture current_store currency/options/allow USD
     * @magentoConfigFixture current_store general/locale/code en_US
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGeneratePlaceOrderRequestLoggedInCustomerWithBothDefaultAddressesReturnsRequestWithValidAddressesOnStoreLevel(): void // phpcs:ignore
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getPlaceOrderRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $requestBillingAddress = $request['billing_address'];
        $requestShippingAddress = $request['shipping_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertSame($requestBillingAddress, $requestShippingAddress);
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/customer_us_with_address_same_billing_shipping.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGeneratePlaceOrderRequestLoggedInCustomerWithBothDefaultAddressesAndVirtualCartReturnsRequestWithValidAddressesOnDefaultLevel(): void // phpcs:ignore
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'virtual-product', 1);

        $request = $this->getPlaceOrderRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData()
        );
        $requestBillingAddress = $request['billing_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertTrue(!isset($request['shipping_address']));
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/customer_us_with_address_same_billing_shipping.php
     *
     * @magentoConfigFixture current_store general/country/default US
     * @magentoConfigFixture current_store general/store_information/country_id US
     * @magentoConfigFixture current_store general/store_information/region_id 82
     * @magentoConfigFixture current_store tax/defaults/country US
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/discount_tax 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id US
     * @magentoConfigFixture current_store shipping/origin/region_id 1
     * @magentoConfigFixture current_store tax/display/shipping 1
     * @magentoConfigFixture current_store tax/display/type 1
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 1
     * @magentoConfigFixture default/currency/options/base USD
     * @magentoConfigFixture current_store currency/options/default USD
     * @magentoConfigFixture current_store currency/options/allow USD
     * @magentoConfigFixture current_store general/locale/code en_US
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGeneratePlaceOrderRequestLoggedInCustomerWithBothDefaultAddressesAndVirtualCartReturnsRequestWithValidAddressesOnStoreLevel(): void // phpcs:ignore
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'virtual-product', 1);

        $request = $this->getPlaceOrderRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData()
        );
        $requestBillingAddress = $request['billing_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertTrue(!isset($request['shipping_address']));
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGeneratePlaceOrderRequestGuestCustomerReturnsRequestWithValidAddressesOnDefaultLevel(): void
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getPlaceOrderRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $requestBillingAddress = $request['billing_address'];
        $requestShippingAddress = $request['shipping_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertSame($requestBillingAddress, $requestShippingAddress);
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store general/country/default US
     * @magentoConfigFixture current_store general/store_information/country_id US
     * @magentoConfigFixture current_store general/store_information/region_id 82
     * @magentoConfigFixture current_store tax/defaults/country US
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/discount_tax 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id US
     * @magentoConfigFixture current_store shipping/origin/region_id 1
     * @magentoConfigFixture current_store tax/display/shipping 1
     * @magentoConfigFixture current_store tax/display/type 1
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 1
     * @magentoConfigFixture default/currency/options/base USD
     * @magentoConfigFixture current_store currency/options/default USD
     * @magentoConfigFixture current_store currency/options/allow USD
     * @magentoConfigFixture current_store general/locale/code en_US
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGeneratePlaceOrderRequestGuestCustomerReturnsRequestWithValidAddressesOnStoreLevel(): void
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getPlaceOrderRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $requestBillingAddress = $request['billing_address'];
        $requestShippingAddress = $request['shipping_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertSame($requestBillingAddress, $requestShippingAddress);
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGeneratePlaceOrderRequestGuestCustomerAndVirtualCartReturnsRequestWithValidAddressesOnDefaultLevel(): void
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'virtual-product', 1);

        $request = $this->getPlaceOrderRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $requestBillingAddress = $request['billing_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertTrue(!isset($request['shipping_address']));
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     *
     * @magentoConfigFixture current_store general/country/default US
     * @magentoConfigFixture current_store general/store_information/country_id US
     * @magentoConfigFixture current_store general/store_information/region_id 82
     * @magentoConfigFixture current_store tax/defaults/country US
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/discount_tax 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id US
     * @magentoConfigFixture current_store shipping/origin/region_id 1
     * @magentoConfigFixture current_store tax/display/shipping 1
     * @magentoConfigFixture current_store tax/display/type 1
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 1
     * @magentoConfigFixture default/currency/options/base USD
     * @magentoConfigFixture current_store currency/options/default USD
     * @magentoConfigFixture current_store currency/options/allow USD
     * @magentoConfigFixture current_store general/locale/code en_US
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGeneratePlaceOrderRequestGuestCustomerAndVirtualCartReturnsRequestWithValidAddressesOnStoreLevel(): void
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'virtual-product', 1);

        $request = $this->getPlaceOrderRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $requestBillingAddress = $request['billing_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertTrue(!isset($request['shipping_address']));
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGeneratePlaceOrderRequestEmptyQuoteImpliesThrowingExceptionOnDefaultLevel(): void
    {
        $quote = $this->session->getQuote();

        self::expectException(KlarnaApiException::class);
        $this->requestBuilder
            ->generatePlaceOrderRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
    }

    /**
     *
     * @magentoConfigFixture current_store general/country/default US
     * @magentoConfigFixture current_store general/store_information/country_id US
     * @magentoConfigFixture current_store general/store_information/region_id 82
     * @magentoConfigFixture current_store tax/defaults/country US
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/discount_tax 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id US
     * @magentoConfigFixture current_store shipping/origin/region_id 1
     * @magentoConfigFixture current_store tax/display/shipping 1
     * @magentoConfigFixture current_store tax/display/type 1
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 1
     * @magentoConfigFixture default/currency/options/base USD
     * @magentoConfigFixture current_store currency/options/default USD
     * @magentoConfigFixture current_store currency/options/allow USD
     * @magentoConfigFixture current_store general/locale/code en_US
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGeneratePlaceOrderRequestEmptyQuoteImpliesThrowingExceptionOnStoreLevel(): void
    {
        $quote = $this->session->getQuote();

        self::expectException(KlarnaApiException::class);
        $this->requestBuilder
            ->generatePlaceOrderRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
    }

    /**
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateCreateSessionRequestEmptyQuoteImpliesThrowingExceptionOnDefaultLevel(): void
    {
        $quote = $this->session->getQuote();

        self::expectException(KlarnaApiException::class);
        $this->requestBuilder
            ->generateCreateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
    }

    /**
     *
     * @magentoConfigFixture current_store general/country/default US
     * @magentoConfigFixture current_store general/store_information/country_id US
     * @magentoConfigFixture current_store general/store_information/region_id 82
     * @magentoConfigFixture current_store tax/defaults/country US
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/discount_tax 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id US
     * @magentoConfigFixture current_store shipping/origin/region_id 1
     * @magentoConfigFixture current_store tax/display/shipping 1
     * @magentoConfigFixture current_store tax/display/type 1
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 1
     * @magentoConfigFixture default/currency/options/base USD
     * @magentoConfigFixture current_store currency/options/default USD
     * @magentoConfigFixture current_store currency/options/allow USD
     * @magentoConfigFixture current_store general/locale/code en_US
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateCreateSessionRequestEmptyQuoteImpliesThrowingExceptionOnStoreLevel(): void
    {
        $quote = $this->session->getQuote();

        self::expectException(KlarnaApiException::class);
        $this->requestBuilder
            ->generateCreateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
    }

    /**
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateUpdateSessionRequestEmptyQuoteImpliesThrowingExceptionOnDefaultLevel(): void
    {
        $quote = $this->session->getQuote();

        self::expectException(KlarnaApiException::class);
        $this->requestBuilder
            ->generateUpdateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
    }

    /**
     *
     * @magentoConfigFixture current_store general/country/default US
     * @magentoConfigFixture current_store general/store_information/country_id US
     * @magentoConfigFixture current_store general/store_information/region_id 82
     * @magentoConfigFixture current_store tax/defaults/country US
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/discount_tax 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id US
     * @magentoConfigFixture current_store shipping/origin/region_id 1
     * @magentoConfigFixture current_store tax/display/shipping 1
     * @magentoConfigFixture current_store tax/display/type 1
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 1
     * @magentoConfigFixture default/currency/options/base USD
     * @magentoConfigFixture current_store currency/options/default USD
     * @magentoConfigFixture current_store currency/options/allow USD
     * @magentoConfigFixture current_store general/locale/code en_US
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateUpdateSessionRequestEmptyQuoteImpliesThrowingExceptionOnStoreLevel(): void
    {
        $quote = $this->session->getQuote();

        self::expectException(KlarnaApiException::class);
        $this->requestBuilder
            ->generateUpdateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store payment/klarna_kp/data_sharing 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateCreateSessionRequestSameContentLikeMethodGenerateUpdateSessionRequestOnDefaultLevel(): void
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $createSessionRequest = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $updateSessionRequest = $this
            ->requestBuilder
            ->generateUpdateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();

        unset(
            $createSessionRequest['merchant_urls']['authorization'],
            $updateSessionRequest['merchant_urls']['authorization']
        );
        static::assertEquals($createSessionRequest, $updateSessionRequest);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store payment/klarna_kp/data_sharing 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateCreateSessionRequestSameContentLikeMethodGenerateUpdateSessionRequestOnDefaultLevelNoAuthToken(): void
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $createSessionRequest = $this->getCreateSessionRequestNoAuthToken(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $updateSessionRequest = $this
            ->requestBuilder
            ->generateUpdateSessionRequest($quote, null)
            ->toArray();

        unset(
            $createSessionRequest['merchant_urls']['authorization'],
            $updateSessionRequest['merchant_urls']['authorization']
        );
        static::assertEquals($createSessionRequest, $updateSessionRequest);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store general/country/default US
     * @magentoConfigFixture current_store general/store_information/country_id US
     * @magentoConfigFixture current_store general/store_information/region_id 82
     * @magentoConfigFixture current_store tax/defaults/country US
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/discount_tax 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id US
     * @magentoConfigFixture current_store shipping/origin/region_id 1
     * @magentoConfigFixture current_store tax/display/shipping 1
     * @magentoConfigFixture current_store tax/display/type 1
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 1
     * @magentoConfigFixture default/currency/options/base USD
     * @magentoConfigFixture current_store currency/options/default USD
     * @magentoConfigFixture current_store currency/options/allow USD
     * @magentoConfigFixture current_store general/locale/code en_US
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store payment/klarna_kp/data_sharing 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateCreateSessionRequestSameContentLikeMethodGenerateUpdateSessionRequestOnStoreLevel(): void
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $createSessionRequest = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $updateSessionRequest = $this
            ->requestBuilder
            ->generateUpdateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();

        unset(
            $createSessionRequest['merchant_urls']['authorization'],
            $updateSessionRequest['merchant_urls']['authorization']
        );
        static::assertEquals($createSessionRequest, $updateSessionRequest);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store payment/klarna_kp/data_sharing 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateCreateSessionRequestSameContentWithPlaceOrderRequestForSameNodesOnDefaultLevel(): void
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $createSessionRequest = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $placeOrderRequest = $this
            ->requestBuilder
            ->generatePlaceOrderRequest($quote, 'a-random-auth-callback-token')
            ->toArray();

        unset(
            $createSessionRequest['merchant_urls'],
            $placeOrderRequest['merchant_urls'],
            $placeOrderRequest['merchant_reference1'],
            $placeOrderRequest['merchant_reference2']
        );
        static::assertEquals($createSessionRequest, $placeOrderRequest);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store general/country/default US
     * @magentoConfigFixture current_store general/store_information/country_id US
     * @magentoConfigFixture current_store general/store_information/region_id 82
     * @magentoConfigFixture current_store tax/defaults/country US
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/discount_tax 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id US
     * @magentoConfigFixture current_store shipping/origin/region_id 1
     * @magentoConfigFixture current_store tax/display/shipping 1
     * @magentoConfigFixture current_store tax/display/type 1
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 1
     * @magentoConfigFixture default/currency/options/base USD
     * @magentoConfigFixture current_store currency/options/default USD
     * @magentoConfigFixture current_store currency/options/allow USD
     * @magentoConfigFixture current_store general/locale/code en_US
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store payment/klarna_kp/data_sharing 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateCreateSessionRequestSameContentWithPlaceOrderRequestForSameNodesOnStoreLevel(): void
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $createSessionRequest = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $placeOrderRequest = $this
            ->requestBuilder
            ->generatePlaceOrderRequest($quote, 'a-random-auth-callback-token')
            ->toArray();

        unset(
            $createSessionRequest['merchant_urls'],
            $placeOrderRequest['merchant_urls'],
            $placeOrderRequest['merchant_reference1'],
            $placeOrderRequest['merchant_reference2']
        );
        static::assertEquals($createSessionRequest, $placeOrderRequest);
    }
}
