<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Initialization\Payload;

use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Kp\Model\Initialization\Payload\RequestFetcher;
use Magento\Quote\Model\Quote;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Initialization\Payload\RequestFetcher
 */
class PayloadTest extends TestCase
{
    /**
     * @var RequestFetcher
     */
    private RequestFetcher $payload;
    /**
     * @var Quote
     */
    private Quote $quote;

    public function testGetRequestReturningPayload(): void
    {
        $parameter = [
            'my_result_key' => 'my_result_value',
            'additional_input' => [
                'auth_callback_token' => 'abc'
            ]
        ];
        $this->dependencyMocks['quoteFetcher']->method('getMagentoQuote')
            ->with($parameter)
            ->willReturn($this->quote);

        $expectedResult = ['my_result_key' => 'my_result_value'];
        $this->dependencyMocks['requestBuilder']->method('getRequest')
            ->with($this->quote, $parameter['additional_input']['auth_callback_token'])
            ->willReturn($expectedResult);

        static::assertEquals($expectedResult, $this->payload->getRequest($parameter));
    }

    protected function setUp(): void
    {
        $this->payload = parent::setUpMocks(RequestFetcher::class);
        $this->quote = $this->mockFactory->create(Quote::class);
    }
}