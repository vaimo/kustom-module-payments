<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder\Nodes;

use Klarna\Kp\Model\Api\Request\Builder;

/**
 * @internal
 */
class OrderAmount
{
    /**
     * Adding the order amount to the request
     *
     * @param Builder $requestBuilder
     */
    public function addToRequest(Builder $requestBuilder): void
    {
        $requestBuilder->setOrderAmount($this->calculateOrderTotalAmount($requestBuilder));
    }

    /**
     * Calculate the order total amount
     *
     * @param Builder $builder
     * @return int
     */
    private function calculateOrderTotalAmount(Builder $builder): int
    {
        $totalAmount = 0;
        foreach ($builder->getOrderLines() as $orderLine) {
            $totalAmount += $orderLine->getTotal();
        }

        return (int) $totalAmount;
    }
}
