<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder\Nodes;

use Klarna\Base\Exception as KlarnaException;
use Klarna\Kp\Model\Api\Builder\Nodes\OrderLines;
use Klarna\Kp\Model\Api\Request\Builder;
use Klarna\Orderlines\Model\Container\Parameter;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Quote\Model\Quote;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\OrderLines
 */
class OrderLinesTest extends TestCase
{
    /**
     * @var OrderLines
     */
    private OrderLines $model;
    /**
     * @var Builder
     */
    private Builder $requestBuilder;
    /**
     * @var Parameter
     */
    private Parameter $parameter;
    /**
     * @var Quote
     */
    private Quote $quote;

    public function testAddToRequestEmptyOrderLineList(): void
    {
        $this->parameter->method('getOrderLines')
            ->willReturn([]);
        $this->requestBuilder->expects(static::never())
            ->method('addOrderlines');
        self::expectException(KlarnaException::class);

        $this->model->addToRequest($this->requestBuilder, $this->parameter, $this->quote);
    }

    public function testAddToRequestSettingToRequest(): void
    {
        $target = ['a' => 'b'];
        $this->parameter->method('getOrderLines')
            ->willReturn($target);
        $this->requestBuilder->expects(static::once())
            ->method('addOrderlines')
            ->with($target);

        $this->model->addToRequest($this->requestBuilder, $this->parameter, $this->quote);
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(OrderLines::class);

        $this->requestBuilder = $this->mockFactory->create(Builder::class);
        $this->parameter = $this->mockFactory->create(Parameter::class);
        $this->quote = $this->mockFactory->create(Quote::class);
    }
}
