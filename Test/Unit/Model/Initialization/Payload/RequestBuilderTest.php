<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Initialization\Payload;

use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Kp\Model\Api\Request as BuilderRequest;
use Klarna\Kp\Model\Initialization\Payload\RequestBuilder;
use Magento\Quote\Model\Quote;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Initialization\Payload\RequestBuilder
 */
class RequestBuilderTest extends TestCase
{
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var RequestBuilder
     */
    private RequestBuilder $requestBuilder;
    /**
     * @var BuilderRequest
     */
    private BuilderRequest $builderRequest;

    public function testGetRequestAuthCallbackUrlIsPartOfTheMerchantUrls(): void
    {
        $this->dependencyMocks['builder']->method('generateCreateSessionRequest')
            ->with($this->quote)
            ->willReturn($this->builderRequest);

        $generatedKpRequest = [
            'merchant_urls' => [
                'authorization' => 'authorization_value'
            ]
        ];
        $this->builderRequest->method('toArray')
            ->willReturn($generatedKpRequest);
        $request = $this->requestBuilder->getRequest($this->quote, '1');
        static::assertTrue(isset($request['merchant_urls']['authorization']));
    }

    protected function setUp(): void
    {
        $this->requestBuilder = parent::setUpMocks(RequestBuilder::class);

        $this->quote = $this->mockFactory->create(Quote::class);
        $this->builderRequest = $this->mockFactory->create(BuilderRequest::class);
    }
}