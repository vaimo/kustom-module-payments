<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Gateway\Command;

use Magento\Payment\Gateway\Command\CommandException;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Kp\Gateway\Command\Authorize;
use Magento\Payment\Model\Info;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @coversDefaultClass \Klarna\Kp\Gateway\Handler\TitleHandler
 */
class AuthorizeTest extends TestCase
{
    /**
     * @var TitleHandler
     */
    private $authorizeCommand;
    /**
     * @var Info
     */
    private $payment;
    /**
     * @var Order
     */
    private Order $magentoOrder;
    /**
     * @var \stdClass
     */
    private \stdClass $paymentStdClass;

    /**
     * @dataProvider multipleThrowableProvider
     * @param \Throwable $throwable
     * @return void
     */
    public function testCommandCanHandleAnyTypeOfThrowableAndIsNotLimited(\Throwable $throwable): void
    {
        $this->paymentStdClass->method('getPayment')
            ->willThrowException($throwable);

        $this->expectException(CommandException::class);

        $this->authorizeCommand->execute([
            'payment' => $this->paymentStdClass
        ]);
    }

    public function testExecuteNoCustomerIdIsAssignedToTheOrderImpliesNoAccessTokenUpdate(): void
    {
        $this->dependencyMocks['service']->expects(static::never())
            ->method('markAccessTokenAsUsedByCustomerId');
        static::assertNull($this->authorizeCommand->execute(['payment' => $this->paymentStdClass]));
    }

    public function testExecuteCustomerIdIsAssignedToTheOrderAsStringImpliesNoAccessTokenUpdate(): void
    {
        $expected = '1';
        $this->magentoOrder->method('getCustomerId')
            ->willReturn($expected);
        $this->dependencyMocks['service']->expects(static::once())
            ->method('markAccessTokenAsUsedByCustomerId')
            ->with($expected);
        static::assertNull($this->authorizeCommand->execute(['payment' => $this->paymentStdClass]));
    }

    public function testExecuteCustomerIdIsAssignedToTheOrderAsIntegerImpliesNoAccessTokenUpdate(): void
    {
        $this->magentoOrder->method('getCustomerId')
            ->willReturn(1);
        $this->dependencyMocks['service']->expects(static::once())
            ->method('markAccessTokenAsUsedByCustomerId')
            ->with('1');
        static::assertNull($this->authorizeCommand->execute(['payment' => $this->paymentStdClass]));
    }

    /**
     * This function provides different types of Throwable
     * @return array
     */
    public function multipleThrowableProvider(): array
    {
        return [
            [
                'throwable' => $this->createMock(\Exception::class),
            ],
            [
                'throwable' => $this->createMock(\TypeError::class),
            ],
            [
                'throwable' => $this->createMock(\Error::class),
            ],
            [
                'throwable' => $this->createMock(\RuntimeException::class),
            ],
        ];
    }

    /**
     * Basic setup for test
     */
    protected function setUp(): void
    {
        $this->authorizeCommand = parent::setUpMocks(Authorize::class);

        $this->payment = $this->mockFactory->create(Info::class, [], ['getOrder']);
        $this->magentoOrder = $this->mockFactory->create(Order::class);
        $this->payment->method('getOrder')
            ->willReturn($this->magentoOrder);

        $magentoQuote = $this->mockFactory->create(Quote::class);
        $this->dependencyMocks['magentoQuoteRepository']->method('get')
            ->willReturn($magentoQuote);

        $klarnaQuote = $this->mockFactory->create(\Klarna\Kp\Model\Quote::class);
        $this->dependencyMocks['klarnaQuoteRepository']->method('getActiveByQuote')
            ->willReturn($klarnaQuote);

        $this->paymentStdClass = $this->mockFactory->create(\stdClass::class, [], ['getPayment']);
        $this->paymentStdClass->method('getPayment')
            ->willReturn($this->payment);
    }
}
