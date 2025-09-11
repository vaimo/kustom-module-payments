<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\QuoteProvider;

use Klarna\Kp\Model\QuoteProvider\PersistenceAwareQuoteProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Klarna\Base\Test\Unit\Mock\TestCase;

/**
 * @coversDefaultClass \Klarna\Kp\Model\QuoteProvider\PersistenceAwareQuoteProvider
 */
class PersistenceAwareQuoteProviderTest extends TestCase
{
    /**
     * @var PersistenceAwareQuoteProvider|MockObject
     */
    private $subject;

    public function testItShouldReturnNullWhenNoOrderIdInRequest(): void
    {
        $result = $this->subject->getQuote();

        $this->assertNull($result);
    }

    public function testItShouldReturnQuoteWhenRequestHasTestIdAndOrderFoundInRepo(): void
    {
        $this->dependencyMocks['request']
            ->method('getParam')
            ->with('order_id')
            ->willReturn(42);

        $order = $this->mockFactory->create(OrderInterface::class);
        $order->method('getQuoteId')->willReturn(99);

        $this->dependencyMocks['orderRepository']
            ->method('get')
            ->with(42)
            ->willReturn($order);

        $quote = $this->mockFactory->create(CartInterface::class);
        $this->dependencyMocks['quoteRepository']
            ->method('get')
            ->with(99)
            ->willReturn($quote);

        $result = $this->subject->getQuote();

        $this->assertInstanceOf(CartInterface::class, $result);
    }

    public function testItShouldReturnNullWhenNotFoundInBothOrderRepositoryAndSession(): void
    {
        $this->dependencyMocks['request']
            ->method('getParam')
            ->with('order_id')
            ->willReturn(42);

        $order = $this->mockFactory->create(OrderInterface::class);
        $order->method('getQuoteId')->willReturn(99);

        $this->dependencyMocks['orderRepository']
            ->method('get')
            ->with(42)
            ->willThrowException(new NoSuchEntityException());

        $result = $this->subject->getQuote();

        $this->assertNull($result);
    }

    protected function setUp(): void
    {
        $this->subject = parent::setUpMocks(PersistenceAwareQuoteProvider::class);
    }
}
