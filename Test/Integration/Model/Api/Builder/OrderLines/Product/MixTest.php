<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Integration\Model\Api\Builder\OrderLines\Product;

use Klarna\Base\Test\Integration\Helper\RequestBuilderTestCase;

/**
 * @group orderlines_tests
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @internal
 */
class MixTest extends RequestBuilderTestCase
{
    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralSimpleProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'simple', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralSimpleProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'simple', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralVirtualProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralVirtualProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralDownloadableProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralDownloadableProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralSimpleProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'simple', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralSimpleProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'simple', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralVirtualProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralVirtualProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

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
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralSimpleProductsAndSeveralVirtualProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

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
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralSimpleProductsAndSeveralVirtualProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralSimpleProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'simple', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralSimpleProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'simple', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralVirtualProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralVirtualProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralDownloadableProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralDownloadableProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralSimpleProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'simple', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralSimpleProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'simple', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralVirtualProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData()
        );
        $orderLines = $request['order_lines'];

        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
        $this->prePurchaseValidator->isKlarnaShippingOrderlineItemMissing($orderLines);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralVirtualProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData()
        );
        $orderLines = $request['order_lines'];

        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
        $this->prePurchaseValidator->isKlarnaShippingOrderlineItemMissing($orderLines);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralSimpleProductsAndSeveralVirtualProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

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
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralSimpleProductsAndSeveralVirtualProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }


    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralGroupedProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralGroupedProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralGroupedProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralGroupedProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralGroupedProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralGroupedProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralGroupedProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralGroupedProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

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
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralSimpleProductsAndSeveralGroupedProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

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
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralSimpleProductsAndSeveralGroupedProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

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
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralSimpleProductsAndSeveralGroupedProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

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
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralSimpleProductsAndSeveralGroupedProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralVirtualProductsAndSeveralGroupedProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralVirtualProductsAndSeveralGroupedProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralVirtualProductsAndSeveralGroupedProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralVirtualProductsAndSeveralGroupedProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralConfigurableProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralConfigurableProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralConfigurableProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralBundledProductsAndSeveralConfigurableProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralConfigurableProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralConfigurableProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralConfigurableProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDownloadableProductsAndSeveralConfigurableProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralGroupedProductsAndSeveralConfigurableProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralGroupedProductsAndSeveralConfigurableProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralGroupedProductsAndSeveralConfigurableProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralGroupedProductsAndSeveralConfigurableProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

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
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralSimpleProductsAndSeveralConfigurableProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

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
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralSimpleProductsAndSeveralConfigurableProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

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
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralSimpleProductsAndSeveralConfigurableProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

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
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralSimpleProductsAndSeveralConfigurableProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'simple', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralVirtualProductsAndSeveralConfigurableProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralVirtualProductsAndSeveralConfigurableProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralVirtualProductsAndSeveralConfigurableProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralVirtualProductsAndSeveralConfigurableProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralSimpleProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'simple', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralSimpleProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'simple', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralVirtualProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralVirtualProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralDownloadableProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralDownloadableProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralSimpleProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'simple', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralSimpleProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'simple', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralVirtualProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralVirtualProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'virtual-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralDownloadableProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralDownloadableProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'downloadable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralGroupedProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralGroupedProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralGroupedProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralGroupedProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'grouped-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralConfigurableProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralConfigurableProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralConfigurableProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralConfigurableProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'configurable', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralFixedBundledProductsOnDefaultLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralFixedBundledProductsOnStoreLevelForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'USD',
            $this->dataProvider->getUsAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralUsChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralFixedBundledProductsOnDefaultLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture current_store currency/options/default EUR
     * @magentoConfigFixture current_store currency/options/allow EUR
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSeveralDynamicBundledProductsAndSeveralFixedBundledProductsOnStoreLevelForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, 'bundle-product-dynamic', 3);
        $this->quotePreparer->addProduct($quote, 'bundle-product', 3);

        $request = $this->getCreateSessionRequest(
            $quote,
            'EUR',
            $this->dataProvider->getDeAddressData(),
            'flatrate_flatrate'
        );
        $this->prePurchaseValidator->performAllGeneralChecks($request, $quote);
    }
}
