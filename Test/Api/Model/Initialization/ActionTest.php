<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Api\Model\Initialization;

use Klarna\Base\Test\Integration\Helper\ApiRequestTestCase;
use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Model\Initialization\Action;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Directory\Model\Currency;

/**
 * @internal
 */
class ActionTest extends ApiRequestTestCase
{
    /**
     * @var Action
     */
    private $action;
    /**
     * @var Currency
     */
    private $currency;

    public const MARKET_EU = 'eu';
    public const MARKET_US = 'us';
    public const MARKET_NZ = 'nz';
    public const WRONG_MARKET_ERROR = 'Invalid market';

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = $this->objectManager->get(Action::class);
        $this->currency = $this->objectManager->get(Currency::class);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
     *
     * @magentoConfigFixture default/general/country/default DE
     * @magentoConfigFixture default/general/store_information/country_id DE
     * @magentoConfigFixture default/general/store_information/region_id 82
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
     * @magentoConfigFixture default/currency/options/base EUR
     * @magentoConfigFixture default/currency/options/default EUR
     * @magentoConfigFixture default/currency/options/allow EUR
     * @magentoConfigFixture default/general/locale/code de_DE
     *
     * @magentoConfigFixture current_store klarna/api/region eu
     * @magentoConfigFixture current_store klarna/api_eu/api_mode 1
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSendRequestSendingApiRequestForDeGuestWithEuApiSetupImpliesSuccessfulApiRequestOnDefaultLevel(): void
    {
        $this->setupTestForMarket(
            [
                'market' => self::MARKET_EU,
                'currency' => 'EUR'
            ],
            $this->session->getQuote()
        );
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
     *
     * @magentoConfigFixture current_store general/country/default DE
     * @magentoConfigFixture current_store general/store_information/country_id DE
     * @magentoConfigFixture current_store general/store_information/region_id 82
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
     * @magentoConfigFixture current_store general/locale/code de_DE
     *
     * @magentoConfigFixture current_store klarna/api/region eu
     * @magentoConfigFixture current_store klarna/api_eu/api_mode 1
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSendRequestSendingApiRequestForDeGuestWithEuApiSetupImpliesSuccessfulApiRequestOnStoreLevel(): void
    {
        $this->setupTestForMarket(
            [
                'market' => self::MARKET_EU,
                'currency' => 'EUR'
            ],
            $this->session->getQuote()
        );
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store klarna/api/region na
     * @magentoConfigFixture current_store klarna/api_us/api_mode 1
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSendRequestSendingApiRequestForUsGuestWithUsApiSetupImpliesSuccessfulApiRequestOnDefaultLevel(): void
    {
        $this->setupTestForMarket(
            [
                'market' => self::MARKET_US,
                'currency' => 'USD'
            ],
            $this->session->getQuote()
        );
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
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 1
     * @magentoConfigFixture default/currency/options/base USD
     * @magentoConfigFixture current_store currency/options/default USD
     * @magentoConfigFixture current_store currency/options/allow USD
     * @magentoConfigFixture current_store general/locale/code en_US
     *
     * @magentoConfigFixture current_store klarna/api/region na
     * @magentoConfigFixture current_store klarna/api_us/api_mode 1
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSendRequestSendingApiRequestForUsGuestWithUsApiSetupImpliesSuccessfulApiRequestOnStoreLevel(): void
    {
        $this->setupTestForMarket(
            [
                'market' => self::MARKET_US,
                'currency' => 'USD'
            ],
            $this->session->getQuote()
        );
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_nz_postal_6011.php
     *
     * @magentoConfigFixture default/general/country/default NZ
     * @magentoConfigFixture default/general/store_information/country_id NZ
     * @magentoConfigFixture default/tax/defaults/country NZ
     * @magentoConfigFixture default/tax/calculation/price_includes_tax 1
     * @magentoConfigFixture default/tax/calculation/shipping_includes_tax 1
     * @magentoConfigFixture default/tax/calculation/discount_tax 1
     * @magentoConfigFixture default/tax/display/shipping 2
     * @magentoConfigFixture default/tax/display/type 2
     * @magentoConfigFixture default/shipping/origin/country_id NZ
     * @magentoConfigFixture default/shipping/origin/region_id 82
     * @magentoConfigFixture default/tax/display/shipping 2
     * @magentoConfigFixture default/tax/display/type 2
     * @magentoConfigFixture default/tax/classes/shipping_tax_class 2
     * @magentoConfigFixture default/currency/options/base NZD
     * @magentoConfigFixture default/currency/options/default NZD
     * @magentoConfigFixture default/currency/options/allow NZD
     * @magentoConfigFixture default/general/locale/code NZ
     *
     * @magentoConfigFixture current_store klarna/api/region oc
     * @magentoConfigFixture current_store klarna/api_nz/api_mode 1
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSendRequestSendingApiRequestForNzGuestWithEuApiSetupImpliesSuccessfulApiRequestOnDefaultLevel(): void
    {
        $rates = ['NZD' => ['USD' => '1']];
        $this->currency->saveRates($rates);

        $this->setupTestForMarket(
            [
                'market' => self::MARKET_NZ,
                'currency' => 'NZD'
            ],
            $this->session->getQuote()
        );
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store general/country/default NZ
     * @magentoConfigFixture current_store general/store_information/country_id NZ
     * @magentoConfigFixture current_store tax/defaults/country NZ
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/discount_tax 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id NZ
     * @magentoConfigFixture current_store shipping/origin/region_id 1
     * @magentoConfigFixture current_store tax/display/shipping 1
     * @magentoConfigFixture current_store tax/display/type 1
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 1
     * @magentoConfigFixture default/currency/options/base NZD
     * @magentoConfigFixture current_store currency/options/default NZD
     * @magentoConfigFixture current_store currency/options/allow NZD
     * @magentoConfigFixture current_store general/locale/code en_US
     *
     * @magentoConfigFixture current_store klarna/api/region oc
     * @magentoConfigFixture current_store klarna/api_nz/api_mode 1
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSendRequestSendingApiRequestForNzGuestWithEuApiSetupImpliesSuccessfulApiRequestOnStoreLevel(): void
    {
        $rates = ['NZD' => ['USD' => '1']];
        $this->currency->saveRates($rates);

        $this->setupTestForMarket(
            [
                'market' => self::MARKET_NZ,
                'currency' => 'NZD'
            ],
            $this->session->getQuote()
        );
    }

    private function setupTestForMarket(array $marketData, CartInterface|Quote $quote): void
    {
        $quote->save();

        $this->configureKlarnaCredentials($quote->getStore(), $marketData['market']);
        $quote->setBaseCurrencyCode($marketData['currency']);

        $product = $this->productRepository->get('simple');
        $quote->addProduct($product);

        // Check what market we are testing and fetch the address related to that market, otherwise throw error
        $address = match ($marketData['market']) {
            self::MARKET_EU => $this->dataProvider->getDeAddressData(),
            self::MARKET_US => $this->dataProvider->getUsAddressData(),
            self::MARKET_NZ => $this->dataProvider->getNzAddressData(),
            default => throw new \InvalidArgumentException(self::WRONG_MARKET_ERROR),
        };
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);
        $quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');

        $quote->setTotalsCollectedFlag(false);
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $quote->save();

        $klarnaQuote = $this->action->sendRequest($quote);
        $this->checkResult($klarnaQuote, $quote);
    }

    private function checkResult(QuoteInterface $klarnaQuote, CartInterface|Quote $magentoQuote): void
    {
        static::assertNotEmpty($klarnaQuote->getSessionId());
        static::assertNotEmpty($klarnaQuote->getClientToken());
        static::assertNotEmpty($klarnaQuote->getQuoteId());
        static::assertNotEmpty($klarnaQuote->getPaymentMethods());
        static::assertNotEmpty($klarnaQuote->getPaymentMethodInfo());
        static::assertNotEmpty($klarnaQuote->getAuthTokenCallbackToken());
        static::assertNotEmpty($klarnaQuote->getCustomerType());
        static::assertEquals($magentoQuote->getId(), $klarnaQuote->getQuoteId());
    }
}
