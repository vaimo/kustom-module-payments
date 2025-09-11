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
use Klarna\Kp\Model\Quote;
use Klarna\Kp\Model\QuoteRepository;
use Magento\Framework\App\Request\Http;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class AuthorizationTokenUpdateTest extends ControllerTestCase
{

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
    public function testExecuteNoAuthorizationTokenGivenImpliesNoKlarnaQuoteSaveOperationOnStoreLevel(): void
    {
        $magentoQuote = $this->init();

        $result = $this->sendRequest(
            [],
            'checkout/klarna/authorizationTokenUpdate',
            Http::METHOD_PUT
        );

        $klarnaQuote = $this->getKlarnaQuote($magentoQuote);
        static::assertEquals(400, $result['statusCode']);
        static::assertEmpty($klarnaQuote->getAuthorizationToken());
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
    public function testExecuteEmptyAuthorizationTokenGivenImpliesNoKlarnaQuoteSaveOperationOnStoreLevel(): void
    {
        $magentoQuote = $this->init();

        $result = $this->sendRequest(
            ['authorization_token' => ''],
            'checkout/klarna/authorizationTokenUpdate',
            Http::METHOD_PUT
        );

        $klarnaQuote = $this->getKlarnaQuote($magentoQuote);
        static::assertEquals(400, $result['statusCode']);
        static::assertEmpty($klarnaQuote->getAuthorizationToken());
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
    public function testExecuteValidAuthorizationTokenGivenImpliesKlarnaQuoteSaveOperationOnStoreLevel(): void
    {
        $magentoQuote = $this->init();

        $result = $this->sendRequest(
            ['authorization_token' => 'abc'],
            'checkout/klarna/authorizationTokenUpdate',
            Http::METHOD_PUT
        );

        $klarnaQuote = $this->getKlarnaQuote($magentoQuote);
        static::assertEquals(200, $result['statusCode']);
        static::assertEquals('abc', $klarnaQuote->getAuthorizationToken());
    }

    private function init(): CartInterface
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        $klarnaQuote = $this->objectManager->get(Quote::class);
        $klarnaQuote->setQuoteId($magentoQuote->getId());
        $klarnaQuote->setIsActive(1);
        $klarnaQuote->save();

        return $magentoQuote;
    }

    private function getKlarnaQuote(CartInterface $magentoQuote): Quote
    {
        $klarnaRepository = $this->objectManager->get(QuoteRepository::class);
        return $klarnaRepository->getActiveByQuote($magentoQuote);
    }
}
