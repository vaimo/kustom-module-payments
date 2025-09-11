<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder\Nodes;

use Klarna\Kp\Model\Api\Builder\Nodes\OrderAmount;
use Klarna\Kp\Model\Api\Request\Builder;
use Klarna\Kp\Model\Api\Request\Orderline;
use Klarna\Base\Test\Unit\Mock\TestCase;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\OrderAmount
 */
class OrderAmountTest extends TestCase
{
    /**
     * @var OrderAmount
     */
    private OrderAmount $orderAmount;
    /**
     * @var Builder
     */
    private Builder $requestBuilder;

    public function testAddToRequestQuote(): void
    {
        $targetValue = 200;
        $this->requestBuilder->expects(static::once())
            ->method('setOrderAmount')
            ->with($targetValue);

        $orderLine = $this->createMock(Orderline::class);
        $orderLine
            ->method('getTotal')
            ->willReturn(100);
        $this->requestBuilder->expects(static::once())
            ->method('getOrderLines')
            ->willReturn([
                $orderLine,
                $orderLine
            ]);

        $this->orderAmount->addToRequest($this->requestBuilder);
    }

    protected function setUp(): void
    {
        $this->orderAmount = parent::setUpMocks(OrderAmount::class);
        $this->requestBuilder = $this->mockFactory->create(Builder::class);
    }
}
