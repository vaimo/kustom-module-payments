<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Placement;

use Klarna\Base\Exception as KlarnaBaseException;
use Klarna\Kp\Api\CreditApiInterface;
use Klarna\Kp\Model\Api\Request;
use Klarna\Kp\Model\Api\Response;
use Klarna\Kp\Model\Api\Rest\Service\Payments;
use Klarna\Kp\Model\Placement\Api;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Kp\Model\Quote as KpQuote;
use Magento\Quote\Model\Quote as MagentoQuote;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Placement\Api
 */
class ApiTest extends TestCase
{
    /**
     * @var Api
     */
    private Api $api;
    /**
     * @var KpQuote
     */
    private KpQuote $klarnaQuote;
    /**
     * @var MagentoQuote
     */
    private MagentoQuote $magentoQuote;
    /**
     * @var Response
     */
    private Response $response;

    public function testPlaceKlarnaOrderNoKecSessionImpliesSettingSessionIdInLoggerContainer(): void
    {
        $this->response->method('isSuccessfull')
            ->willReturn(true);

        $this->dependencyMocks['api']
            ->method('placeOrder')
            ->willReturn($this->response);

        $this->dependencyMocks['container']->expects($this->once())
            ->method('setSessionId');
        static::assertSame(
            $this->response,
            $this->api->placeKlarnaOrder(
                $this->klarnaQuote,
                $this->magentoQuote,
                '1',
                '2'
            )
        );
    }

    public function testPlaceKlarnaOrderKecSessionImpliesNotSettingSessionIdInLoggerContainer(): void
    {
        $this->response->method('isSuccessfull')
            ->willReturn(true);

        $this->dependencyMocks['api']
            ->method('placeOrder')
            ->willReturn($this->response);
        $this->klarnaQuote->method('isKecSession')
            ->willReturn(true);

        $this->dependencyMocks['container']->expects($this->never())
            ->method('setSessionId');
        static::assertSame(
            $this->response,
            $this->api->placeKlarnaOrder(
                $this->klarnaQuote,
                $this->magentoQuote,
                '1',
                '2'
            )
        );
    }

    public function testPlaceKlarnaOrderPlacingFailsImpliesThrowingException(): void
    {
        $this->response->method('isSuccessfull')
            ->willReturn(false);

        $this->dependencyMocks['api']
            ->method('placeOrder')
            ->willReturn($this->response);

        self::expectException(KlarnaBaseException::class);

        $this->api->placeKlarnaOrder(
            $this->klarnaQuote,
            $this->magentoQuote,
            '1',
            '2'
        );
    }

    public function testPlaceKlarnaOrderPlacingSuccessfulAndNoRedirectUrlExistsInResponseImpliesNotSettingUrl(): void
    {
        $this->response->method('isSuccessfull')
            ->willReturn(true);

        $this->dependencyMocks['api']
            ->method('placeOrder')
            ->willReturn($this->response);

        $this->response->method('getRedirectUrl')
            ->willReturn(null);
        $this->klarnaQuote->expects($this->never())
            ->method('setRedirectUrl');
        static::assertSame(
            $this->response,
            $this->api->placeKlarnaOrder(
                $this->klarnaQuote,
                $this->magentoQuote,
                '1',
                '2'
            )
        );
    }

    public function testPlaceKlarnaOrderPlacingSuccessfulAndRedirectUrlExistsInResponseImpliesSettingUrl(): void
    {
        $this->response->method('isSuccessfull')
            ->willReturn(true);

        $this->dependencyMocks['api']
            ->method('placeOrder')
            ->willReturn($this->response);

        $this->response->method('getRedirectUrl')
            ->willReturn('abc');
        $this->klarnaQuote->expects($this->once())
            ->method('setRedirectUrl');
        static::assertSame(
            $this->response,
            $this->api->placeKlarnaOrder(
                $this->klarnaQuote,
                $this->magentoQuote,
                '1',
                '2'
            )
        );
    }

    protected function setUp(): void
    {
        $payments = $this->createSingleMock(Payments::class);

        $this->api = parent::setUpMocks(Api::class,
            [],
            [
                CreditApiInterface::class => $payments
            ]);

        $this->klarnaQuote = $this->mockFactory->create(KpQuote::class);
        $this->magentoQuote = $this->mockFactory->create(
            MagentoQuote::class,
            [],
            [
            'getBaseCurrencyCode'
            ]
        );
        $this->response = $this->mockFactory->create(Response::class);
        $request = $this->mockFactory->create(Request::class);

        $this->dependencyMocks['request']
            ->method('generatePlaceOrderRequest')
            ->willReturn($request);
        $this->klarnaQuote->method('getAuthTokenCallbackToken')
            ->willReturn('3');
        $this->klarnaQuote->method('getSessionId')
            ->willReturn('2');
        $this->magentoQuote->method('getBaseCurrencyCode')
            ->willReturn('EUR');
    }
}
