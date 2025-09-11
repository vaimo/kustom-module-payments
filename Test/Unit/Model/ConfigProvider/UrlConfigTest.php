<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Unit\Model\ConfigProvider;

use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Kp\Model\ConfigProvider\UrlConfig;
use PHPUnit\Framework\MockObject\MockObject;

class UrlConfigTest extends TestCase
{
    /**
     * @var UrlConfig|MockObject
     */
    private UrlConfig $urlConfig;

    public function testGetUrlsReturnsArray():void
    {
        $expected = [
            'reload_checkout_config_url' => 'my-url',
            'redirect_url' => 'my-url',
            'get_quote_status_url' => 'my-url',
            'authorization_token_update_url' => 'my-url',
            'update_quote_email_url' => 'my-url',
            'current_session_data_url' => 'my-url',
        ];

        $this->dependencyMocks['urlBuilder']
            ->method('getUrl')
            ->willReturn('my-url');

        $response = $this->urlConfig->getUrls();
        self::assertEquals($expected, $response);
    }

    protected function setUp(): void
    {
        $this->urlConfig = parent::setUpMocks(UrlConfig::class);
    }
}