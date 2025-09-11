<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder\Nodes;

use Klarna\Base\Exception as KlarnaException;
use Klarna\Kp\Model\Api\Builder\Nodes\OrderTaxAmount;
use Klarna\Kp\Model\Api\Request\Builder;
use Klarna\Orderlines\Model\Container\Parameter;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Quote\Model\Quote;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\OrderTaxAmount
 */
class OrderTaxAmountTest extends TestCase
{
    /**
     * @var OrderTaxAmount
     */
    private OrderTaxAmount $model;
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
            ->method('setOrderTaxAmount');
        self::expectException(KlarnaException::class);

        $this->model->addToRequest($this->requestBuilder, $this->parameter, $this->quote);
    }

    public function testAddToRequestSummingTotalTaxAmount(): void
    {
        $orderLines = [
            [
                'type' => 'a',
                'total_tax_amount' => 5
            ],
            [
                'type' => 'a',
                'total_tax_amount' => 7
            ]
        ];
        $this->parameter->method('getOrderLines')
            ->willReturn($orderLines);
        $this->requestBuilder->expects(static::once())
            ->method('setOrderTaxAmount')
            ->with(12);

        $this->model->addToRequest($this->requestBuilder, $this->parameter, $this->quote);
    }

    public function testAddToRequestFoundSalesTax(): void
    {
        $orderLines = [
            [
                'type' => 'a',
                'total_tax_amount' => 5
            ],
            [
                'type' => 'sales_tax',
                'total_amount' => 7
            ]
        ];
        $this->parameter->method('getOrderLines')
            ->willReturn($orderLines);
        $this->requestBuilder->expects(static::once())
            ->method('setOrderTaxAmount')
            ->with(7);

        $this->model->addToRequest($this->requestBuilder, $this->parameter, $this->quote);
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(OrderTaxAmount::class);

        $this->requestBuilder = $this->mockFactory->create(Builder::class);
        $this->parameter = $this->mockFactory->create(Parameter::class);
        $this->quote = $this->mockFactory->create(Quote::class);
    }
}
