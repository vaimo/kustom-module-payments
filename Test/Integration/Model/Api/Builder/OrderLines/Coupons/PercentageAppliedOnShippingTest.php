<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Integration\Model\Api\Builder\OrderLines\Coupons;

use Klarna\Base\Test\Integration\Helper\RequestBuilderTestCase;

/**
 * @group orderlines_tests
 * @internal
 */
class PercentageAppliedOnShippingTest extends RequestBuilderTestCase
{

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_12_34_discount.php
     *
     * @magentoConfigFixture default/tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCouponLowerGrandTotalOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_12_34_discount.php
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
     * @magentoConfigFixture default/currency/options/base USD
     * @magentoConfigFixture current_store currency/options/default USD
     * @magentoConfigFixture current_store currency/options/allow USD
     * @magentoConfigFixture current_store general/locale/code en_US
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCouponLowerGrandTotalOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_100_discount.php
     *
     * @magentoConfigFixture default/tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testFullCouponOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_100_discount.php
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
     * @magentoConfigFixture default/currency/options/base USD
     * @magentoConfigFixture current_store currency/options/default USD
     * @magentoConfigFixture current_store currency/options/allow USD
     * @magentoConfigFixture current_store general/locale/code en_US
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testFullCouponOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_12_34_discount.php
     *
     * @magentoConfigFixture default/general/country/default DE
     * @magentoConfigFixture default/general/store_information/country_id DE
     * @magentoConfigFixture default/tax/defaults/country DE
     * @magentoConfigFixture default/tax/calculation/price_includes_tax 1
     * @magentoConfigFixture default/tax/calculation/shipping_includes_tax 1
     * @magentoConfigFixture default/tax/calculation/discount_tax 1
     * @magentoConfigFixture default/tax/display/shipping 2
     * @magentoConfigFixture default/tax/display/type 2
     * @magentoConfigFixture default/shipping/origin/country_id DE
     * @magentoConfigFixture default/shipping/origin/region_id 82
     * @magentoConfigFixture default/tax/display/shipping 2
     * @magentoConfigFixture default/tax/display/type 2
     * @magentoConfigFixture default/tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCouponLowerGrandTotalOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_12_34_discount.php
     *
     * @magentoConfigFixture current_store general/country/default DE
     * @magentoConfigFixture current_store general/store_information/country_id DE
     * @magentoConfigFixture current_store tax/defaults/country DE
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/discount_tax 1
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id DE
     * @magentoConfigFixture current_store shipping/origin/region_id 82
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCouponLowerGrandTotalOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_100_discount.php
     *
     * @magentoConfigFixture default/general/country/default DE
     * @magentoConfigFixture default/general/store_information/country_id DE
     * @magentoConfigFixture default/tax/defaults/country DE
     * @magentoConfigFixture default/tax/calculation/price_includes_tax 1
     * @magentoConfigFixture default/tax/calculation/shipping_includes_tax 1
     * @magentoConfigFixture default/tax/calculation/discount_tax 1
     * @magentoConfigFixture default/tax/display/shipping 2
     * @magentoConfigFixture default/tax/display/type 2
     * @magentoConfigFixture default/shipping/origin/country_id DE
     * @magentoConfigFixture default/shipping/origin/region_id 82
     * @magentoConfigFixture default/tax/display/shipping 2
     * @magentoConfigFixture default/tax/display/type 2
     * @magentoConfigFixture default/tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testFullCouponOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_100_discount.php
     *
     * @magentoConfigFixture current_store general/country/default DE
     * @magentoConfigFixture current_store general/store_information/country_id DE
     * @magentoConfigFixture current_store tax/defaults/country DE
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/discount_tax 1
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id DE
     * @magentoConfigFixture current_store shipping/origin/region_id 82
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testFullCouponOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_uk_postal_W13_3BG.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_12_34_discount.php
     *
     * @magentoConfigFixture default/general/country/default GB
     * @magentoConfigFixture default/general/store_information/country_id GB
     * @magentoConfigFixture default/tax/defaults/country GB
     * @magentoConfigFixture default/tax/calculation/price_includes_tax 0
     * @magentoConfigFixture default/tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture default/tax/calculation/discount_tax 0
     * @magentoConfigFixture default/tax/display/shipping 2
     * @magentoConfigFixture default/tax/display/type 2
     * @magentoConfigFixture default/shipping/origin/country_id GB
     * @magentoConfigFixture default/shipping/origin/region_id Greater London
     * @magentoConfigFixture default/tax/display/shipping 2
     * @magentoConfigFixture default/tax/display/type 2
     * @magentoConfigFixture default/tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCouponLowerGrandTotalOnDefaultLevelForShopSetup3()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'GBP',
            $this->dataProvider->getUkAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_uk_postal_W13_3BG.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_12_34_discount.php
     *
     * @magentoConfigFixture current_store general/country/default GB
     * @magentoConfigFixture current_store general/store_information/country_id GB
     * @magentoConfigFixture current_store tax/defaults/country GB
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/discount_tax 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id GB
     * @magentoConfigFixture current_store shipping/origin/region_id Greater London
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCouponLowerGrandTotalOnStoreLevelForShopSetup3()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'GBP',
            $this->dataProvider->getUkAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_uk_postal_W13_3BG.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_100_discount.php
     *
     * @magentoConfigFixture default/general/country/default GB
     * @magentoConfigFixture default/general/store_information/country_id GB
     * @magentoConfigFixture default/tax/defaults/country GB
     * @magentoConfigFixture default/tax/calculation/price_includes_tax 0
     * @magentoConfigFixture default/tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture default/tax/calculation/discount_tax 0
     * @magentoConfigFixture default/tax/display/shipping 2
     * @magentoConfigFixture default/tax/display/type 2
     * @magentoConfigFixture default/shipping/origin/country_id GB
     * @magentoConfigFixture default/shipping/origin/region_id Greater London
     * @magentoConfigFixture default/tax/display/shipping 2
     * @magentoConfigFixture default/tax/display/type 2
     * @magentoConfigFixture default/tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testFullCouponOnDefaultLevelForShopSetup3()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'GBP',
            $this->dataProvider->getUkAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_uk_postal_W13_3BG.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_100_discount.php
     *
     * @magentoConfigFixture current_store general/country/default GB
     * @magentoConfigFixture current_store general/store_information/country_id GB
     * @magentoConfigFixture current_store tax/defaults/country GB
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/discount_tax 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id GB
     * @magentoConfigFixture current_store shipping/origin/region_id Greater London
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testFullCouponOnStoreLevelForShopSetup3()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'GBP',
            $this->dataProvider->getUkAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_12_34_discount.php
     *
     * @magentoConfigFixture default/general/country/default DE
     * @magentoConfigFixture default/general/store_information/country_id DE
     * @magentoConfigFixture default/tax/defaults/country DE
     * @magentoConfigFixture default/tax/calculation/price_includes_tax 1
     * @magentoConfigFixture default/tax/calculation/shipping_includes_tax 1
     * @magentoConfigFixture default/tax/calculation/discount_tax 1
     * @magentoConfigFixture default/tax/calculation/apply_after_discount 0
     * @magentoConfigFixture default/tax/display/shipping 2
     * @magentoConfigFixture default/tax/display/type 2
     * @magentoConfigFixture default/shipping/origin/country_id DE
     * @magentoConfigFixture default/shipping/origin/region_id 82
     * @magentoConfigFixture default/tax/display/shipping 2
     * @magentoConfigFixture default/tax/display/type 2
     * @magentoConfigFixture default/tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCouponLowerGrandTotalOnDefaultLevelForShopSetup4()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $orderLines = $request['order_lines'];
        $fullTotalTaxAmount = $this->prePurchaseValidator->getFullTotalTaxAmount($orderLines);
        $orderTaxAmount = $request['order_tax_amount'];

        static::assertEquals(66, $fullTotalTaxAmount);
        static::assertEquals(66, $orderTaxAmount);
        $this->prePurchaseValidator->isKlarnaSumTotalsShopGrandTotalSame($orderLines, $quote);
        $this->prePurchaseValidator->isKlarnaShippingTotalEqualUnitQty($orderLines);
        $this->prePurchaseValidator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->prePurchaseValidator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->prePurchaseValidator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->prePurchaseValidator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
        $this->prePurchaseValidator->isKlarnaOrderTaxAmountShopTaxSame($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_12_34_discount.php
     *
     * @magentoConfigFixture current_store general/country/default DE
     * @magentoConfigFixture current_store general/store_information/country_id DE
     * @magentoConfigFixture current_store tax/defaults/country DE
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/discount_tax 1
     * @magentoConfigFixture current_store tax/calculation/apply_after_discount 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id DE
     * @magentoConfigFixture current_store shipping/origin/region_id 82
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCouponLowerGrandTotalOnStoreLevelForShopSetup4()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $orderLines = $request['order_lines'];
        $fullTotalTaxAmount = $this->prePurchaseValidator->getFullTotalTaxAmount($orderLines);
        $orderTaxAmount = $request['order_tax_amount'];

        static::assertEquals(104, $fullTotalTaxAmount);
        static::assertEquals(104, $orderTaxAmount);
        $this->prePurchaseValidator->isKlarnaSumTotalsShopGrandTotalSame($orderLines, $quote);
        $this->prePurchaseValidator->isKlarnaShippingTotalEqualUnitQty($orderLines);
        $this->prePurchaseValidator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->prePurchaseValidator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->prePurchaseValidator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->prePurchaseValidator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_100_discount.php
     *
     * @magentoConfigFixture default/general/country/default DE
     * @magentoConfigFixture default/general/store_information/country_id DE
     * @magentoConfigFixture default/tax/defaults/country DE
     * @magentoConfigFixture default/tax/calculation/price_includes_tax 1
     * @magentoConfigFixture default/tax/calculation/shipping_includes_tax 1
     * @magentoConfigFixture default/tax/calculation/discount_tax 1
     * @magentoConfigFixture default/tax/calculation/apply_after_discount 0
     * @magentoConfigFixture default/tax/display/shipping 2
     * @magentoConfigFixture default/tax/display/type 2
     * @magentoConfigFixture default/shipping/origin/country_id DE
     * @magentoConfigFixture default/shipping/origin/region_id 82
     * @magentoConfigFixture default/tax/display/shipping 2
     * @magentoConfigFixture default/tax/display/type 2
     * @magentoConfigFixture default/tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testFullCouponOnDefaultLevelForShopSetup4()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $orderLines = $request['order_lines'];
        $fullTotalTaxAmount = $this->prePurchaseValidator->getFullTotalTaxAmount($orderLines);
        $orderTaxAmount = $request['order_tax_amount'];

        static::assertEquals(0, $fullTotalTaxAmount);
        static::assertEquals(0, $orderTaxAmount);
        $this->prePurchaseValidator->isKlarnaSumTotalsShopGrandTotalSame($orderLines, $quote);
        $this->prePurchaseValidator->isKlarnaShippingTotalEqualUnitQty($orderLines);
        $this->prePurchaseValidator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->prePurchaseValidator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->prePurchaseValidator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->prePurchaseValidator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
        $this->prePurchaseValidator->isKlarnaOrderTaxAmountShopTaxSame($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_apply_to_shipping_100_discount.php
     *
     * @magentoConfigFixture current_store general/country/default DE
     * @magentoConfigFixture current_store general/store_information/country_id DE
     * @magentoConfigFixture current_store tax/defaults/country DE
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/discount_tax 1
     * @magentoConfigFixture current_store tax/calculation/apply_after_discount 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id DE
     * @magentoConfigFixture current_store shipping/origin/region_id 82
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testFullCouponOnStoreLevelForShopSetup4()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 1);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $orderLines = $request['order_lines'];
        $fullTotalTaxAmount = $this->prePurchaseValidator->getFullTotalTaxAmount($orderLines);
        $orderTaxAmount = $request['order_tax_amount'];

        static::assertEquals(38, $fullTotalTaxAmount);
        static::assertEquals(38, $orderTaxAmount);
        $this->prePurchaseValidator->isKlarnaSumTotalsShopGrandTotalSame($orderLines, $quote);
        $this->prePurchaseValidator->isKlarnaShippingTotalEqualUnitQty($orderLines);
        $this->prePurchaseValidator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->prePurchaseValidator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->prePurchaseValidator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->prePurchaseValidator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
    }
}
