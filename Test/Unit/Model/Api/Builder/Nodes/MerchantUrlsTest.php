<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder\Nodes;

use Klarna\Kp\Model\Api\Builder\Nodes\MerchantUrls;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Kp\Model\Api\Request\Builder;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\MerchantUrls
 */
class MerchantUrlsTest extends TestCase
{

    /**
     * @var MerchantUrls
     */
    private MerchantUrls $model;
    /**
     * @var Builder
     */
    private Builder $requestBuilder;

    public function testAddToRequestSetsUrlsToRequest(): void
    {
        $targetUrl = 'my_url';
        $this->dependencyMocks['url']->method('getDirectUrl')
            ->willReturn($targetUrl);

        $expected = [
            'confirmation' => $targetUrl,
            'notification' => $targetUrl,
            'authorization' => $targetUrl
        ];

        $this->requestBuilder->expects(static::once())
            ->method('setMerchantUrls')
            ->with($expected);

        $this->model->addToRequest($this->requestBuilder, 'a-random-auth-callback-token');
    }

    public function testAddToRequestWontAddAuthorizationKeyOnEmptyAuthorizationTokenPassed(): void
    {
        $targetUrl = 'my_url';
        $this->dependencyMocks['url']->method('getDirectUrl')
            ->willReturn($targetUrl);

        $expected = [
            'confirmation' => $targetUrl,
            'notification' => $targetUrl
        ];

        $this->requestBuilder->expects(static::once())
            ->method('setMerchantUrls')
            ->with($expected);

        $this->model->addToRequest($this->requestBuilder);
    }

    public function testAddToRequestSetsAuthorizationCallbackUrl(): void
    {
        $this->dependencyMocks['url']->method('getDirectUrl')
            ->willReturn('my_url');
        $expected = [
            'authorization' => 'my_url',
            'confirmation' => 'my_url',
            'notification' => 'my_url'
        ];
        $this->requestBuilder->expects(static::once())
            ->method('setMerchantUrls')
            ->with($expected);

        $this->model->addToRequest($this->requestBuilder, 'a-random-auth-callback-token');
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(MerchantUrls::class);
        $this->requestBuilder = $this->mockFactory->create(Builder::class);
    }
}
