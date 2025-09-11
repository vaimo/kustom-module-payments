<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Unit\Model\Initialization;

use Klarna\Base\Model\Api\Exception as KlarnaApiException;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Kp\Model\Api\Rest\Service\Payments;
use Klarna\Kp\Model\Initialization\DuplicateRequestBlocker;
use Klarna\Kp\Model\Quote as KlarnaQuote;
use Klarna\Kp\Model\Api\Request;
use Klarna\Kp\Api\Data\ResponseInterface;
use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Model\Initialization\KlarnaQuoteHandler;
use Magento\Quote\Model\Quote;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Initialization\KlarnaQuoteHandler
 */
class KlarnaQuoteHandlerTest extends TestCase
{
    /**
     * @var KlarnaQuoteHandler
     */
    private $klarnaQuoteHandler;
    /**
     * @var Quote|MockObject
     */
    private Quote $magentoQuote;
    /**
     * @var ResponseInterface|MockObject
     */
    private ResponseInterface $response;
    /**
     * @var Request|MockObject
     */
    private Request $request;
    /**
     * @var KlarnaQuote|MockObject
     */
    private KlarnaQuote $klarnaQuote;
    /**
     * @var Payments|MockObject
     */
    private Payments $apiMock;

    public function testCreateKlarnaQuoteSuccess(): void
    {
        $this->apiMock
            ->expects(static::once())
            ->method('createSession')
            ->willReturn($this->response);

        $this->response
            ->method('isSuccessfull')
            ->willReturn(true);

        $this->request
            ->method('getCustomerType')
            ->willReturn('person');

        $this->request
            ->method('toArray')
            ->willReturn(
                [
                    'klarna_quote_id' => 'klarna_quote_id',
                    'merchant_urls'   => [],
                ]
            );

        $this->dependencyMocks['klarnaQuoteFactory']
            ->method('create')
            ->willReturn($this->klarnaQuote);

        $result = $this->klarnaQuoteHandler->createKlarnaQuote($this->magentoQuote, true);

        static::assertInstanceOf(QuoteInterface::class, $result);
    }

    public function testCreateKlarnaQuoteDifferentPayloadSuccess(): void
    {
        $this->response
            ->method('isSuccessfull')
            ->willReturnOnConsecutiveCalls(false, true);

        $this->response
            ->method('getResponseCode')
            ->willReturnOnConsecutiveCalls(500, 200);

        $this->request
            ->method('toArray')
            ->willReturnCallback(function () {
                static $callCount = 1;
                $callCount++;

                return [
                    'klarna_quote_id' => $callCount,
                    'merchant_urls'   => [],
                ];
            });

        $this->dependencyMocks['requestBuilder']
            ->method('generateCreateSessionRequest')
            ->willReturn($this->request);

        $this->apiMock
            ->method('createSession')
            ->willReturn($this->response);

        $this->dependencyMocks['requestBuilder']
            ->method('generateUpdateSessionRequest')
            ->willReturn($this->request);

        $this->dependencyMocks['klarnaQuoteFactory']
            ->method('create')
            ->willReturn($this->klarnaQuote);

        try {
            $result = $this->klarnaQuoteHandler->createKlarnaQuote($this->magentoQuote);
            static::assertNotInstanceOf(QuoteInterface::class, $result);
        } catch (KlarnaApiException $e) {}

        $result = $this->klarnaQuoteHandler->createKlarnaQuote($this->magentoQuote);
        static::assertInstanceOf(QuoteInterface::class, $result);
    }

    public function testCreateKlarnaQuoteDuplicateRequest(): void
    {
        $this->request
            ->method('toArray')
            ->willReturn(
                [
                    'klarna_quote_id' => 'klarna_quote_id',
                    'merchant_urls'   => [],
                ]
            );

        $this->response
            ->method('isSuccessfull')
            ->willReturn(false);

        $this->apiMock
            ->expects(static::once())
            ->method('createSession')
            ->willReturn($this->response);

        $this->dependencyMocks['klarnaQuoteFactory']
            ->method('create')
            ->willReturn($this->klarnaQuote);

        try {
            $this->klarnaQuoteHandler->createKlarnaQuote($this->magentoQuote);
        } catch (KlarnaApiException $e) {}

        $result = $this->klarnaQuoteHandler->createKlarnaQuote($this->magentoQuote);
        static::assertInstanceOf(QuoteInterface::class, $result);
    }

    public function testCreateKlarnaQuoteUnauthorized(): void
    {
        $this->response
            ->method('isSuccessfull')
            ->willReturn(false);

        $this->response
            ->method('getResponseCode')
            ->willReturn(401);

        $this->apiMock
            ->expects(static::once())
            ->method('createSession')
            ->willReturn($this->response);

        $this->request
            ->method('toArray')
            ->willReturn(
                [
                    'klarna_quote_id' => 'klarna_quote_id',
                    'merchant_urls'   => [],
                ]
            );

        $this->dependencyMocks['klarnaQuoteFactory']
            ->method('create')
            ->willReturn($this->klarnaQuote);

        static::expectException(KlarnaApiException::class);
        $this->klarnaQuoteHandler->createKlarnaQuote($this->magentoQuote);
    }

    public function testUpdateKlarnaQuoteSuccess(): void
    {
        $this->klarnaQuote
            ->method('requestDataHaveChanged')
            ->willReturn(true);

        $this->response
            ->method('isSuccessfull')
            ->willReturn(true);

        $this->apiMock
            ->expects(static::once())
            ->method('updateSession')
            ->willReturn($this->response);

        $this->request
            ->method('toArray')
            ->willReturn(
                [
                    'klarna_quote_id' => 'klarna_quote_id',
                    'merchant_urls'   => [],
                ]
            );

        $result = $this->klarnaQuoteHandler->updateKlarnaQuote($this->magentoQuote, $this->klarnaQuote);

        static::assertInstanceOf(QuoteInterface::class, $result);
    }

    public function testUpdateKlarnaQuoteExpiredQuote(): void
    {
        $this->klarnaQuote
            ->method('requestDataHaveChanged')
            ->willReturn(true);

        $this->response
            ->method('isExpired')
            ->willReturn(true);

        $this->apiMock
            ->expects(static::once())
            ->method('updateSession')
            ->willReturn($this->response);

        $this->request
            ->method('toArray')
            ->willReturn(
                [
                    'klarna_quote_id' => 'klarna_quote_id',
                    'merchant_urls'   => [],
                ]
            );

        $result = $this->klarnaQuoteHandler->updateKlarnaQuote($this->magentoQuote, $this->klarnaQuote);

        static::assertInstanceOf(QuoteInterface::class, $result);
    }

    public function testUpdateKlarnaQuoteRequestNotSuccess(): void
    {
        $this->klarnaQuote
            ->method('requestDataHaveChanged')
            ->willReturn(true);

        $this->response
            ->method('isSuccessfull')
            ->willReturn(false);

        $this->apiMock
            ->expects(static::once())
            ->method('updateSession')
            ->willReturn($this->response);

        $this->request
            ->method('toArray')
            ->willReturn(
                [
                    'klarna_quote_id' => 'klarna_quote_id',
                    'merchant_urls'   => [],
                ]
            );

        static::expectException(KlarnaApiException::class);
        $this->klarnaQuoteHandler->updateKlarnaQuote($this->magentoQuote, $this->klarnaQuote);
    }

    public function testUpdateKlarnaQuoteDuplicateRequest()
    {
        $this->apiMock
            ->expects(static::never())
            ->method('updateSession')
            ->willReturn($this->response);

        $result = $this->klarnaQuoteHandler->updateKlarnaQuote($this->magentoQuote, $this->klarnaQuote);

        static::assertInstanceOf(QuoteInterface::class, $result);
    }

    protected function setUp(): void
    {
        parent::setUpMocks(KlarnaQuoteHandler::class);

        $this->magentoQuote = $this->createSingleMock(Quote::class, [], ['getBaseCurrencyCode']);
        $this->response = $this->createSingleMock(ResponseInterface::class);
        $this->request = $this->createSingleMock(Request::class);
        $this->klarnaQuote = $this->createSingleMock(KlarnaQuote::class);
        $this->apiMock = $this->createSingleMock(Payments::class);

        $this->klarnaQuoteHandler = new KlarnaQuoteHandler(
            $this->dependencyMocks['klarnaQuoteFactory'],
            $this->dependencyMocks['klarnaQuoteRepository'],
            $this->dependencyMocks['paymentMethodProvider'],
            $this->dependencyMocks['requestBuilder'],
            $this->apiMock,
            $this->dependencyMocks['randomGenerator'],
            $this->dependencyMocks['container'],
            new DuplicateRequestBlocker()
        );

        $this->magentoQuote
            ->method('getBaseCurrencyCode')
            ->willReturn('EUR');

        $this->klarnaQuote
            ->method('getAuthTokenCallbackToken')
            ->willReturn('token');

        $this->klarnaQuote
            ->method('getSessionId')
            ->willReturn('1');

        $this->dependencyMocks['requestBuilder']
            ->method('generateCreateSessionRequest')
            ->willReturn($this->request);

        $this->dependencyMocks['requestBuilder']
            ->method('generateUpdateSessionRequest')
            ->willReturn($this->request);
    }
}