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
use Magento\Framework\App\Request\Http;
use Magento\Framework\Url;
use Klarna\Kp\Model\Quote;

/**
 * @internal
 */
class CookieTest extends ControllerTestCase
{

    /**
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
    public function testExecuteNoMagentoQuoteWasFoundInTheKlarnaTableImpliesReturnsDefaultUrlOnStoreLevel(): void
    {
        $this->sendRequest(
            [],
            'checkout/klarna/cookie',
            Http::METHOD_GET
        );

        $urlInstance = $this->getUrlInstance();
        static::assertEquals('checkout/onepage/success/', $urlInstance->getData()['route_path']);
    }

    /**
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
    public function testExecuteNoActiveKlarnaQuoteFoundImpliedReturnsDefaultUrlOnStoreLevel(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();
        $this->session->setLastQuoteId($magentoQuote->getId());

        $this->sendRequest(
            [],
            'checkout/klarna/cookie',
            Http::METHOD_GET
        );

        $urlInstance = $this->getUrlInstance();
        static::assertEquals('checkout/onepage/success/', $urlInstance->getData()['route_path']);
    }

    /**
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
    public function testExecuteNoOrderIdIsAddedToTheKlarnaQuoteImpliedReturnsDefaultUrlOnStoreLevel(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();
        $this->session->setLastQuoteId($magentoQuote->getId());

        /** @var Quote $klarnaQuote */
        $klarnaQuote = $this->objectManager->get(Quote::class);
        $klarnaQuote->setQuoteId($magentoQuote->getId());
        $klarnaQuote->setRedirectUrl('http://example.com');
        $klarnaQuote->setIsActive(1);
        $klarnaQuote->save();

        $this->sendRequest(
            [],
            'checkout/klarna/cookie',
            Http::METHOD_GET
        );

        $urlInstance = $this->getUrlInstance();
        static::assertEquals('checkout/onepage/success/', $urlInstance->getData()['route_path']);
    }

    /**
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
    public function testExecuteNoOrderIsFoundWithKlarnaQuoteOrderIdImpliedReturnsDefaultUrlOnStoreLevel(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();
        $magentoQuoteId = $magentoQuote->getId();
        $this->session->setLastQuoteId($magentoQuoteId);

        /** @var Quote $klarnaQuote */
        $klarnaQuote = $this->objectManager->get(Quote::class);
        $klarnaQuote->setQuoteId($magentoQuoteId);
        $klarnaQuote->setRedirectUrl('http://example.com');
        $klarnaQuote->setIsActive(1);
        $klarnaQuote->setOrderId("1");
        $klarnaQuote->save();

        $this->sendRequest(
            [],
            'checkout/klarna/cookie',
            Http::METHOD_GET
        );

        $urlInstance = $this->getUrlInstance();
        static::assertEquals('checkout/onepage/success/', $urlInstance->getData()['route_path']);
    }

    /**
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
    public function testExecuteNoRedirectUrlIsAddedToTheKlarnaQuoteImpliedReturnsDefaultUrlOnStoreLevel(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();
        $magentoQuoteId = $magentoQuote->getId();
        $this->session->setLastQuoteId($magentoQuoteId);
        $order = $this->createOrder($magentoQuoteId);

        $klarnaQuote = $this->objectManager->get(Quote::class);
        $klarnaQuote->setQuoteId($magentoQuoteId);
        $klarnaQuote->setIsActive(1);
        $klarnaQuote->setOrderId((string) $order->getId());
        $klarnaQuote->save();

        $this->sendRequest(
            [],
            'checkout/klarna/cookie',
            Http::METHOD_GET
        );

        $urlInstance = $this->getUrlInstance();
        static::assertEquals('checkout/onepage/success/', $urlInstance->getData()['route_path']);
    }

    /**
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
    public function testExecuteRedirectUrlIsReturnedOnStoreLevel(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();
        $magentoQuoteId = $magentoQuote->getId();
        $this->session->setLastQuoteId($magentoQuoteId);
        $order = $this->createOrder($magentoQuoteId);
        
        /** @var Quote $klarnaQuote */
        $klarnaQuote = $this->objectManager->get(Quote::class);
        $klarnaQuote->setOrderId((string) $order->getId());
        $klarnaQuote->setQuoteId($magentoQuoteId);
        $klarnaQuote->setRedirectUrl('http://example.com');
        $klarnaQuote->setIsActive(1);
        $klarnaQuote->save();

        $this->sendRequest(
            [],
            'checkout/klarna/cookie',
            Http::METHOD_GET
        );

        $urlInstance = $this->getUrlInstance();
        static::assertTrue(!isset($urlInstance->getData()['route_path']));
    }

    /**
     * Getting back the URL instance
     *
     * @return Url
     */
    private function getUrlInstance(): Url
    {
        return $this->objectManager->get(Url::class);
    }

    /**
     * Create an order with a payment method set to klarna_kp
     *
     * @param mixed $quoteId
     * @return \Magento\Sales\Model\Order
     */
    private function createOrder($quoteId): \Magento\Sales\Model\Order
    {
        /** @var \Magento\Sales\Model\OrderFactory $orderFactory */
        $orderFactory = $this->objectManager->get(\Magento\Sales\Model\OrderFactory::class);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $orderFactory->create();
        $order->setQuoteId((int) $quoteId);
        $order->setIncrementId('100000001');
        $order->setStatus('processing');
        $order->setState('new');
        $order->setGrandTotal(1190.00);
        $order->setBaseGrandTotal(1190.00);
        $order->setOrderCurrencyCode('USD');
        $order->setBaseCurrencyCode('USD');
        $order->setCustomerEmail('customer@email.us');
        $order->setCustomerFirstname('Test');
        $order->setCustomerLastname('Person-us');
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->objectManager->create(\Magento\Sales\Model\Order\Payment::class);
        $payment->setMethod('klarna_kp');
        $order->setPayment($payment);
        $order->save();

        return $order;
    }
}
