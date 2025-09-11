<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Initialization\Payload;

use Klarna\Kp\Model\Api\Builder\Request;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class RequestBuilder
{
    /**
     * @var Request
     */
    private Request $builder;

    /**
     * @param Request $builder
     * @codeCoverageIgnore
     */
    public function __construct(Request $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Getting back the request
     *
     * @param CartInterface $magentoQuote
     * @param string|null $authCallbackToken
     * @return array
     */
    public function getRequest(CartInterface $magentoQuote, ?string $authCallbackToken): array
    {
        $result = $this->builder->generateCreateSessionRequest($magentoQuote, $authCallbackToken)
            ->toArray();

        $result['intent'] = 'buy';

        return $result;
    }
}
