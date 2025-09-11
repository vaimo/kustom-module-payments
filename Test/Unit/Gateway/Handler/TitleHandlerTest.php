<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Gateway\Handler;

use Klarna\Kp\Model\Quote as KlarnaQuote;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Kp\Gateway\Handler\TitleHandler;
use Magento\Payment\Gateway\Data\PaymentDataObject;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Payment;

/**
 * @coversDefaultClass \Klarna\Kp\Gateway\Handler\TitleHandler
 */
class TitleHandlerTest extends TestCase
{
    /**
     * @var TitleHandler
     */
    private $titleHandler;

    /**
     * The fallback 'Klarna Payments' will be returned, if the title can't be determined
     */
    public function testNoPaymentInstanceGiven(): void
    {
        $actual = $this->titleHandler->handle([]);
        static::assertEquals('Klarna Payments', $actual);
    }

    public function testHandleReturnFromQuote(): void
    {
        $quote = $this->mockFactory->create(Quote::class);
        $subjectPayment = $this->mockFactory->create(PaymentDataObject::class);
        $payment = $this->mockFactory->create(Payment::class);
        $payment->method('getQuote')
            ->willReturn($quote);
        $payment->method('getMethod')
            ->willReturn('payment method');
        $subjectPayment->method('getPayment')
            ->willReturn($payment);

        $subject = [
            'payment' => $subjectPayment
        ];

        $klarnaQuote = $this->mockFactory->create(KlarnaQuote::class);
        $this->dependencyMocks['klarnaQuoteRepository']->method('getActiveByQuote')
            ->willReturn($klarnaQuote);

        $expected = 'my title';
        $this->dependencyMocks['titleProvider']->method('getByKlarnaQuote')
            ->willReturn($expected);
        static::assertEquals($expected, $this->titleHandler->handle($subject));
    }

    public function testHandleReturnFromAdditionalInformation(): void
    {
        $quote = $this->mockFactory->create(Quote::class);
        $subjectPayment = $this->mockFactory->create(PaymentDataObject::class);
        $payment = $this->mockFactory->create(Payment::class);
        $payment->method('getQuote')
            ->willReturn($quote);
        $subjectPayment->method('getPayment')
            ->willReturn($payment);

        $subject = [
            'payment' => $subjectPayment
        ];

        $expected = 'my title';
        $this->dependencyMocks['titleProvider']->method('getByAdditionalInformation')
            ->willReturn($expected);
        static::assertEquals($expected, $this->titleHandler->handle($subject));
    }

    /**
     * Basic setup for test
     */
    protected function setUp(): void
    {
        $this->titleHandler = parent::setUpMocks(TitleHandler::class);
    }
}
