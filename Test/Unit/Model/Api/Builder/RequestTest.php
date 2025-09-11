<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder;

use Klarna\Base\Model\Api\OrderLineProcessor;
use Klarna\Kp\Model\Api\Builder\Request;
use Klarna\Kp\Model\Api\Request\Validator;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Quote\Model\Quote;
use Klarna\Kp\Api\Data\RequestInterface;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Request
 */
class RequestTest extends TestCase
{

    /**
     * @var Request
     */
    private Request $model;
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var OrderLineProcessor
     */
    private OrderLineProcessor $orderLineProcessor;
    /**
     * @var Validator
     */
    private Validator $validator;

    public function testGenerateCreateSessionRequestWithoutAuthCallbackToken(): void
    {
        $this->dependencyMocks['merchantUrls']->expects(static::once())
            ->method('addToRequest')
            ->with($this->dependencyMocks['requestBuilder'], null);

        $createSession = $this
            ->model
            ->generateCreateSessionRequest($this->quote, null);

        static::assertInstanceOf(RequestInterface::class, $createSession);
    }

    public function testGenerateCreateSessionRequestCalledOrderLinesProcessor(): void
    {
        $this->orderLineProcessor->expects(static::once())
            ->method('processByQuote');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionRequestCalledMerchantUrlsLogic():void
    {
        $this->dependencyMocks['merchantUrls']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionRequestCalledOrderLinesLogic(): void
    {
        $this->dependencyMocks['orderLines']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionRequestCalledOrderTaxAmountLogic(): void
    {
        $this->dependencyMocks['orderTaxAmount']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionRequestCalledOrderAmountLogic(): void
    {
        $this->dependencyMocks['orderAmount']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionRequestCalledPurchaseCountryLogic(): void
    {
        $this->dependencyMocks['purchaseCountry']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionRequestCalledMiscellaneousLogic(): void
    {
        $this->dependencyMocks['miscellaneous']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionCalledValidatorLogic(): void
    {
        $this->quote->expects(static::once())
            ->method('getIsVirtual')
            ->willReturn(false);
        $this->validator->expects(static::once())
            ->method('isRequiredValueMissing')
            ->with(
                [
                    'purchase_country',
                    'purchase_currency',
                    'locale',
                    'order_amount',
                    'order_lines',
                    'billing_address',
                    'shipping_address'
                ],
                Request::GENERATE_TYPE_CREATE
            );
        $this->validator->expects(static::once())
            ->method('isSumOrderLinesMatchingOrderAmount');

        $this->model->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionCalledValidatorLogicWhenQuoteIsVirtual(): void
    {
        $this->quote->expects(static::once())
            ->method('getIsVirtual')
            ->willReturn(true);
        $this->validator->expects(static::once())
            ->method('isRequiredValueMissing')
            ->with(
                [
                    'purchase_country',
                    'purchase_currency',
                    'locale',
                    'order_amount',
                    'order_lines',
                    'billing_address'
                ],
                Request::GENERATE_TYPE_CREATE
            );
        $this->validator->expects(static::once())
            ->method('isSumOrderLinesMatchingOrderAmount');

        $this->model->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestWithoutAuthCallbackToken(): void
    {
        $this->orderLineProcessor->expects(static::once())
            ->method('processByQuote');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestCalledOrderLinesProcessor(): void
    {
        $this->dependencyMocks['merchantUrls']->expects(static::once())
            ->method('addToRequest')
            ->with($this->dependencyMocks['requestBuilder'], null);

        $updateSession = $this
            ->model
            ->generateUpdateSessionRequest($this->quote, null);

        static::assertInstanceOf(RequestInterface::class, $updateSession);
    }

    public function testGenerateUpdateSessionRequestCalledMerchantUrlsLogic():void
    {
        $this->dependencyMocks['merchantUrls']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestCalledOrderLinesLogic(): void
    {
        $this->dependencyMocks['orderLines']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestCalledOrderTaxAmountLogic(): void
    {
        $this->dependencyMocks['orderTaxAmount']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestCalledOrderAmountLogic(): void
    {
        $this->dependencyMocks['orderAmount']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestCalledPurchaseCountryLogic(): void
    {
        $this->dependencyMocks['purchaseCountry']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestCalledMiscellaneousLogic(): void
    {
        $this->dependencyMocks['miscellaneous']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionCalledValidatorLogic(): void
    {
        $this->quote->expects(static::once())
            ->method('getIsVirtual')
            ->willReturn(false);
        $this->validator->expects(static::once())
            ->method('isRequiredValueMissing')
            ->with(
                [
                    'purchase_country',
                    'purchase_currency',
                    'locale',
                    'order_amount',
                    'order_lines',
                    'billing_address',
                    'shipping_address'
                ],
                Request::GENERATE_TYPE_UPDATE
            );
        $this->validator->expects(static::once())
            ->method('isSumOrderLinesMatchingOrderAmount');

        $this->model->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionCalledValidatorLogicWhenQuoteIsVirtual(): void
    {
        $this->quote->expects(static::once())
            ->method('getIsVirtual')
            ->willReturn(true);
        $this->validator->expects(static::once())
            ->method('isRequiredValueMissing')
            ->with(
                [
                    'purchase_country',
                    'purchase_currency',
                    'locale',
                    'order_amount',
                    'order_lines',
                    'billing_address'
                ],
                Request::GENERATE_TYPE_UPDATE
            );
        $this->validator->expects(static::once())
            ->method('isSumOrderLinesMatchingOrderAmount');

        $this->model->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledOrderLinesProcessor(): void
    {
        $this->orderLineProcessor->expects(static::once())
            ->method('processByQuote');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledMerchantUrlsLogic():void
    {
        $this->dependencyMocks['merchantUrls']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledOrderLinesLogic(): void
    {
        $this->dependencyMocks['orderLines']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledOrderTaxAmountLogic(): void
    {
        $this->dependencyMocks['orderTaxAmount']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledOrderAmountLogic(): void
    {
        $this->dependencyMocks['orderAmount']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledPurchaseCountryLogic(): void
    {
        $this->dependencyMocks['purchaseCountry']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledCustomerLogic(): void
    {
        $basicData = ['a' => 'b'];
        $this->dependencyMocks['customerGenerator']->expects(static::once())
            ->method('getBasicData')
            ->willReturn($basicData);
        $this->dependencyMocks['requestBuilder']->expects(static::once())
            ->method('setCustomer')
            ->with($basicData);

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledMiscellaneousLogic(): void
    {
        $this->dependencyMocks['miscellaneous']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledAddressLogic(): void
    {
        $this->dependencyMocks['addressBuilder']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledMerchantReferencesLogic(): void
    {
        $this->dependencyMocks['merchantReferences']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestQuoteIsVirtualAndShippingAddressWillNotBeChecked(): void
    {
        $this->quote->expects(static::once())
            ->method('getIsVirtual')
            ->willReturn(true);
        $this->validator->expects(static::once())
            ->method('isRequiredValueMissing')
            ->with(
                [
                    'purchase_country',
                    'purchase_currency',
                    'locale',
                    'order_amount',
                    'order_lines',
                    'billing_address',
                    'merchant_urls',
                ],
                Request::GENERATE_TYPE_PLACE
            );
        $this->validator->expects(static::once())
            ->method('isSumOrderLinesMatchingOrderAmount');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestQuoteIsNotVirtualAndShippingAddressWillBeChecked(): void
    {
        $this->quote->method('getIsVirtual')
            ->willReturn(false);

        $this->validator->expects(static::once())
            ->method('isRequiredValueMissing')
            ->with(
                [
                    'purchase_country',
                    'purchase_currency',
                    'locale',
                    'order_amount',
                    'order_lines',
                    'billing_address',
                    'merchant_urls',
                    'shipping_address'
                ],
                Request::GENERATE_TYPE_PLACE
            );
        $this->validator->expects(static::once())
            ->method('isSumOrderLinesMatchingOrderAmount');

        $this->model->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateFullRequestCallsGeneratePlaceOrderRequestInternally(): void
    {
        $model = $this->getMockBuilder(get_class($this->model))
            ->onlyMethods(['generatePlaceOrderRequest'])
            ->disableOriginalConstructor()
            ->getMock();

        $model->expects(static::once())
            ->method('generatePlaceOrderRequest')
            ->with($this->quote, 'a-random-auth-callback-token');

        $model->generateFullRequest($this->quote, 'a-random-auth-callback-token');
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(Request::class);

        $this->quote = $this->mockFactory->create(Quote::class);
        $this->orderLineProcessor = $this->mockFactory->create(OrderLineProcessor::class);
        $this->dependencyMocks['parameter']->method('getOrderLineProcessor')
            ->willReturn($this->orderLineProcessor);

        $request = $this->mockFactory->create(\Klarna\Kp\Model\Api\Request::class);
        $this->dependencyMocks['requestBuilder']->method('getRequest')
            ->willReturn($request);

        $this->validator = $this->mockFactory->create(Validator::class);
        $this->dependencyMocks['requestBuilder']->method('getValidator')
            ->willReturn($this->validator);
    }
}
