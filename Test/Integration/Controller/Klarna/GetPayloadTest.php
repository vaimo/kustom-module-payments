<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Integration\Controller\Klarna;

use Klarna\Base\Test\Integration\Helper\ControllerTestCase;
use Klarna\Base\Test\Integration\Helper\QuotePreparer;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\QuoteFactory;

/**
 * @internal
 */
class GetPayloadTest extends ControllerTestCase
{
    /**
     * @var QuotePreparer
     */
    private $quotePreparer;
    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->quotePreparer = $this->objectManager->get(QuotePreparer::class);
        $this->quoteFactory = $this->objectManager->get(QuoteFactory::class);
    }

    /**
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
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteCurrentQuoteWithSingleSimpleProductAsGuestOnStoreLevelForShopSetup1(): void
    {
        $this->performCheckOnExistingQuote('simple', $this->dataProvider->getUsAddressData(), 'USD');
    }

    /**
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
    public function testExecuteUsingCurrentQuoteWithSingleVirtualProductAsGuestCustomerOnStoreLevelForShopSetup1(): void
    {
        $this->performCheckOnExistingQuote('virtual-product', $this->dataProvider->getUsAddressData(), 'USD');
    }

    /**
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
    public function testExecuteUsingCurrentQuoteWithSingleDownloadableProductAsGuestCustomerOnStoreLevelForShopSetup1(): void
    {
        $this->performCheckOnExistingQuote('downloadable', $this->dataProvider->getUsAddressData(), 'USD');
    }

    /**
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
    public function testExecuteUsingCurrentQuoteWithSingleGroupedProductAsGuestCustomerOnStoreLevelForShopSetup1(): void
    {
        $this->performCheckOnExistingQuote('grouped-product', $this->dataProvider->getUsAddressData(), 'USD');
    }

    /**
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
    public function testExecuteUsingCurrentQuoteWithSingleConfigurableProductAsGuestCustomerOnStoreLevelForShopSetup1(): void
    {
        $this->performCheckOnExistingQuote('configurable', $this->dataProvider->getUsAddressData(), 'USD');
    }

    /**
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
    public function testExecuteUsingCurrentQuoteWithSingleFixedBundledProductAsGuestCustomerOnStoreLevelForShopSetup1(): void
    {
        $this->performCheckOnExistingQuote('bundle-product', $this->dataProvider->getUsAddressData(), 'USD');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
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
    public function testExecuteUsingCurrentQuoteWithSingleDynamicBundledProductAsGuestCustomerOnStoreLevelForShopSetup1(): void
    {
        $this->performCheckOnExistingQuote('bundle-product-dynamic', $this->dataProvider->getUsAddressData(), 'USD');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_everything.php
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
    public function testExecuteUsingCurrentQuoteWithSingleSimpleProductAsGuestCustomerOnStoreLevelForShopSetup2(): void
    {
        $this->performCheckOnExistingQuote('simple', $this->dataProvider->getDeAddressData(), 'EUR');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_everything.php
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
    public function testExecuteUsingCurrentQuoteWithSingleVirtualProductAsGuestCustomerOnStoreLevelForShopSetup2(): void
    {
        $this->performCheckOnExistingQuote('virtual-product', $this->dataProvider->getDeAddressData(), 'EUR');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_everything.php
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
    public function testExecuteUsingCurrentQuoteWithSingleDownloadableProductAsGuestCustomerOnStoreLevelForShopSetup2(): void
    {
        $this->performCheckOnExistingQuote('downloadable', $this->dataProvider->getDeAddressData(), 'EUR');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_everything.php
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
    public function testExecuteUsingCurrentQuoteWithSingleGroupedProductAsGuestCustomerOnStoreLevelForShopSetup2(): void
    {
        $this->performCheckOnExistingQuote('grouped-product', $this->dataProvider->getDeAddressData(), 'EUR');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_configurable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_everything.php
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
    public function testExecuteUsingCurrentQuoteWithSingleConfigurableProductAsGuestCustomerOnStoreLevelForShopSetup2(): void
    {
        $this->performCheckOnExistingQuote('configurable', $this->dataProvider->getDeAddressData(), 'EUR');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_everything.php
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
    public function testExecuteUsingCurrentQuoteWithSingleFixedBundledProductAsGuestCustomerOnStoreLevelForShopSetup2(): void
    {
        $this->performCheckOnExistingQuote('bundle-product', $this->dataProvider->getDeAddressData(), 'EUR');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_everything.php
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
    public function testExecuteUsingCurrentQuoteWithSingleDynamicBundledProductAsGuestCustomerOnStoreLevelForShopSetup2(): void
    {
        $this->performCheckOnExistingQuote('bundle-product-dynamic', $this->dataProvider->getDeAddressData(), 'EUR');
    }

    /**
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
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteNotCurrentQuoteWithSingleSimpleProductAsGuestOnStoreLevelForShopSetup1(): void
    {
        $this->performCheckOnNewQuote('simple', '99999', $this->dataProvider->getUsAddressData(), 'USD');
    }

    /**
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
    public function testExecuteNotCurrentQuoteWithSingleVirtualProductAsGuestOnStoreLevelForShopSetup1(): void
    {
        $this->performCheckOnNewQuote('virtual-product', '99995', $this->dataProvider->getUsAddressData(), 'USD');
    }

    /**
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
    public function testExecuteNotCurrentQuoteWithSingleDownloadableProductAsGuestOnStoreLevelForShopSetup1(): void
    {
        $this->performCheckOnNewQuote('downloadable', '123456', $this->dataProvider->getUsAddressData(), 'USD');
    }

    /**
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
    public function testExecuteNotCurrentQuoteWithSingleGroupedProductAsGuestCustomerOnStoreLevelForShopSetup1(): void
    {
        $this->performCheckOnNewQuote('grouped-product', '99599', $this->dataProvider->getUsAddressData(), 'USD');
    }

    /**
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
    public function testExecuteNotCurrentQuoteWithSingleFixedBundledProductAsGuestCustomerOnStoreLevelForShopSetup1(): void
    {
        $this->performCheckOnNewQuote('bundle-product', '123', $this->dataProvider->getUsAddressData(), 'USD');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
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
    public function testExecuteNotCurrentQuoteWithSingleDynamicBundledProductAsGuestCustomerOnStoreLevelForShopSetup1(): void
    {
        $this->performCheckOnNewQuote('bundle-product-dynamic', '12', $this->dataProvider->getUsAddressData(), 'USD');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_everything.php
     *
     * @magentoConfigFixture current_store tax/calculation/algorithm ROW_BASE_CALCULATION
     * @magentoConfigFixture current_store general/locale/code de_DE
     * @magentoConfigFixture current_store general/store_information/region_id 82
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteNotCurrentQuoteWithSingleSimpleProductAsGuestOnStoreLevelForShopSetup2(): void
    {
        $this->performCheckOnNewQuote('simple', '99999', $this->dataProvider->getDeAddressData(), 'EUR');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_everything.php
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
    public function testExecuteNotCurrentQuoteWithSingleVirtualProductAsGuestOnStoreLevelForShopSetup2(): void
    {
        $this->performCheckOnNewQuote('virtual-product', '99995', $this->dataProvider->getDeAddressData(), 'EUR');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_downloadable.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_everything.php
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
    public function testExecuteNotCurrentQuoteWithSingleDownloadableProductAsGuestOnStoreLevelForShopSetup2(): void
    {
        $this->performCheckOnNewQuote('downloadable', '123456', $this->dataProvider->getDeAddressData(), 'EUR');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_grouped.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_everything.php
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
    public function testExecuteNotCurrentQuoteWithSingleGroupedProductAsGuestCustomerOnStoreLevelForShopSetup2(): void
    {
        $this->performCheckOnNewQuote('grouped-product', '99599', $this->dataProvider->getDeAddressData(), 'EUR');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_fixed.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_everything.php
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
    public function testExecuteNotCurrentQuoteWithSingleFixedBundledProductAsGuestCustomerOnStoreLevelForShopSetup2(): void
    {
        $this->performCheckOnNewQuote('bundle-product', '123', $this->dataProvider->getDeAddressData(), 'EUR');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_everything.php
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
    public function testExecuteNotCurrentQuoteWithSingleDynamicBundledProductAsGuestCustomerOnStoreLevelForShopSetup2(): void
    {
        $this->performCheckOnNewQuote('bundle-product-dynamic', '12', $this->dataProvider->getDeAddressData(), 'EUR');
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture default/tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteReturningAuthCallUrlInMerchantUrlsFieldOnDefaultLevelForShopSetup1(): void
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $this->quotePreparer->addProduct($quote, 'simple', 1);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUsAddressData();
        $quote->getBillingAddress()->setCountryId($address[AddressInterface::KEY_COUNTRY_ID]);
        $quote->getShippingAddress()->setCountryId($address[AddressInterface::KEY_COUNTRY_ID]);
        $quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');

        $this->quotePreparer->saveQuote($quote);

        $params = [
            'additional_input' => json_encode([
                'use_existing_quote' => '1',
                'auth_callback_token' => 'my_auth_callback_token'
            ]),
            'country_id' => 'US',
            'shipping_method' => 'flatrate',
            'shipping_carrier_code' => 'flatrate'
        ];

        $response = $this->sendRequest($params, 'checkout/klarna/getPayLoad', 'POST');

        $body = $response['body'];
        static::assertTrue(isset($body['merchant_urls']['authorization']));
        static::assertNotEmpty(isset($body['merchant_urls']['authorization']));
    }

    /**
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
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteReturningAuthCallUrlInMerchantUrlsFieldOnStoreLevelForShopSetup1(): void
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $this->quotePreparer->addProduct($quote, 'simple', 1);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUsAddressData();
        $quote->getBillingAddress()->setCountryId($address[AddressInterface::KEY_COUNTRY_ID]);
        $quote->getShippingAddress()->setCountryId($address[AddressInterface::KEY_COUNTRY_ID]);
        $quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');

        $this->quotePreparer->saveQuote($quote);

        $params = [
            'additional_input' => json_encode([
                'use_existing_quote' => '1',
                'auth_callback_token' => 'my_auth_callback_token'
            ]),
            'country_id' => 'US',
            'shipping_method' => 'flatrate',
            'shipping_carrier_code' => 'flatrate'
        ];

        $response = $this->sendRequest($params, 'checkout/klarna/getPayLoad', 'POST');

        $body = $response['body'];
        static::assertTrue(isset($body['merchant_urls']['authorization']));
        static::assertNotEmpty(isset($body['merchant_urls']['authorization']));
    }

    /**
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
    public function testExecuteUsingCurrentQuoteOfGuestCustomerWithNoAuthorizationTokenOnStoreLevelForShopSetup2(): void
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('EUR');

        $this->quotePreparer->addProduct($quote, 'simple', 1);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getDeAddressData();
        $quote->getBillingAddress()->setCountryId($address[AddressInterface::KEY_COUNTRY_ID]);
        $quote->getShippingAddress()->setCountryId($address[AddressInterface::KEY_COUNTRY_ID]);

        $quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');

        $this->quotePreparer->saveQuote($quote);

        $params = [
            'additional_input' => json_encode([
                'use_existing_quote' => '1'
            ]),
            'country_id' => 'DE',
            'shipping_method' => 'flatrate',
            'shipping_carrier_code' => 'flatrate'
        ];

        $response = $this->sendRequest($params, 'checkout/klarna/getPayLoad', 'POST');
        $this->validator->performAllGeneralChecks($response['body'], $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture default/tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteUsingCurrentQuoteOfGuestCustomerWithNoAuthorizationTokenOnDefaultLevelForShopSetup1(): void
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $this->quotePreparer->addProduct($quote, 'simple', 1);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUsAddressData();
        $quote->getBillingAddress()->setCountryId($address[AddressInterface::KEY_COUNTRY_ID]);
        $quote->getShippingAddress()->setCountryId($address[AddressInterface::KEY_COUNTRY_ID]);
        $quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');

        $this->quotePreparer->saveQuote($quote);

        $params = [
            'additional_input' => json_encode([
                'use_existing_quote' => '1'
            ]),
            'country_id' => 'US',
            'shipping_method' => 'flatrate',
            'shipping_carrier_code' => 'flatrate'
        ];

        $response = $this->sendRequest($params, 'checkout/klarna/getPayLoad', 'POST');
        $this->validator->performAllGeneralUsChecks($response['body'], $quote);
    }
    /**
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
    public function testExecuteUsingCurrentQuoteOfGuestCustomerUpdatedShippingMethodOnStoreLevelForShopSetup2(): void
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('EUR');

        $this->quotePreparer->addProduct($quote, 'simple', 1);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getDeAddressData();
        $quote->getBillingAddress()->setCountryId($address[AddressInterface::KEY_COUNTRY_ID]);
        $quote->getShippingAddress()->setCountryId($address[AddressInterface::KEY_COUNTRY_ID]);

        $quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');

        $this->quotePreparer->saveQuote($quote);

        $params = [
            'additional_input' => json_encode([
                'use_existing_quote' => '1'
            ]),
            'country_id' => 'DE',
            'shipping_method' => 'flatrate',
            'shipping_carrier_code' => 'flatrate'
        ];

        $response = $this->sendRequest($params, 'checkout/klarna/getPayLoad', 'POST');
        $this->validator->performAllGeneralChecks($response['body'], $quote);
    }

    /**
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
    public function testExecuteUsingCurrentQuoteOfGuestCustomerUpdatedShippingMethodOnDefaultLevelForShopSetup1(): void
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $this->quotePreparer->addProduct($quote, 'simple', 1);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUsAddressData();
        $quote->getBillingAddress()->setCountryId($address[AddressInterface::KEY_COUNTRY_ID]);
        $quote->getShippingAddress()->setCountryId($address[AddressInterface::KEY_COUNTRY_ID]);
        $quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');

        $this->quotePreparer->saveQuote($quote);

        $params = [
            'additional_input' => json_encode([
                'use_existing_quote' => '1',
            ]),
            'country_id' => 'US',
            'shipping_method' => 'flatrate',
            'shipping_carrier_code' => 'flatrate'
        ];

        $response = $this->sendRequest($params, 'checkout/klarna/getPayLoad', 'POST');
        $this->validator->performAllGeneralUsChecks($response['body'], $quote);
    }

    private function performCheckOnExistingQuote(string $sku, AddressInterface $address, string $currency): void
    {
        $quote = $this->session->getQuote();
        $this->quotePreparer->addProduct($quote, $sku, 1);
        $this->quotePreparer->configureQuoteButAddressJustOnCountryAndShippingMethod($quote, $currency, $address, $address, 'flatrate_flatrate');

        $this->quotePreparer->saveQuote($quote);

        $params = [
            'additional_input' => json_encode([
                'use_existing_quote' => '1',
                'auth_callback_token' => 'my_auth_callback_token'
            ]),
            'country_id' => $address->getCountryId(),
            'shipping_method' => 'flatrate',
            'shipping_carrier_code' => 'flatrate',
        ];

        $response = $this->sendRequest($params, 'checkout/klarna/getPayLoad', 'POST');
        if ($currency === 'USD') {
            $this->validator->performAllGeneralUsChecks($response['body'], $quote);
            $this->validator->isKlarnaOrderTaxAmountZero($response['body']);
        } else {
            $this->validator->performAllGeneralChecks($response['body'], $quote);
            $this->validator->isKlarnaOrderTaxAmountNotZero($response['body']);
        }
    }

    private function performCheckOnNewQuote(string $sku, string $productId, AddressInterface $address, string $currency): void
    {
        $params = [
            'additional_input' => json_encode([
                'use_existing_quote' => '0',
                'auth_callback_token' => 'my_auth_callback_token'
            ]),
            'qty' => '1',
            'product' => $productId
        ];

        $response = $this->sendRequest($params, 'checkout/klarna/getPayLoad', 'POST');
        $this->session->clearQuote();

        $quoteNew = $this->quoteFactory->create();
        $this->quotePreparer->addProduct($quoteNew, $sku, 1);

        $this->quotePreparer->configureQuoteButAddressJustOnCountryAndShippingMethod($quoteNew, $currency, $address, $address, 'flatrate_flatrate');
        $this->quotePreparer->saveQuote($quoteNew);

        if ($currency === 'USD') {
            $this->validator->performAllGeneralUsChecks($response['body'], $quoteNew);
            $this->validator->isKlarnaOrderTaxAmountZero($response['body']);
        } else {
            $this->validator->performAllGeneralChecks($response['body'], $quoteNew);
            $this->validator->isKlarnaOrderTaxAmountNotZero($response['body']);
        }
    }
}