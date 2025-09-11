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
use Klarna\Kp\Model\ConfigProvider\KpConfigProvider;
use Klarna\Kp\Test\Unit\Model\MockObject;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\Store;

class KpConfigProviderTest extends TestCase
{
    /**
     * @var KpConfigProvider
     */
    private KpConfigProvider $kpConfigProvider;
    /**
     * @var Quote|MockObject
     */
    private Quote $quote;

    public function testGetConfigKpNotEnabled(): void
    {
        $this->dependencyMocks['validationHandler']->method('getValidationMessage')
            ->willReturn(__('Klarna Payments will not show up. Reason: aaa, bbb'));

        $result = $this->kpConfigProvider->getConfig();
        static::assertEquals(
            'Klarna Payments will not show up. Reason: aaa, bbb',
            $result['payment']['klarna_kp']['message']
        );
    }

    public function testGetConfigApiRequestWontUpdateKpConfiguration(): void
    {
        $this->dependencyMocks['validationHandler']->method('validateApi')
            ->willReturn(true);

        $this->dependencyMocks['paymentConfigProvider']
            ->method('updateWithQuoteData')
            ->with(
                $this->quote,
                $this->dependencyMocks['paymentConfigProvider']->getPaymentConfig($this->quote->getStore())
            )
            ->willReturn(
                [
                    'payment' => [
                        'klarna_kp' => [
                            'success' => 0,
                        ]
                    ]
                ]
            );

        $result = $this->kpConfigProvider->getConfig();

        self::assertArrayNotHasKey('is_kec_session', $result['payment']['klarna_kp']);
        self::assertEquals( $result['payment']['klarna_kp']['success'], 0);
    }

    public function testGetConfigReturningConfiguration(): void
    {
        $expected = [
            'any_key' => 'Any value',
            [
                'another_any_key' => 'Another any value'
            ]
        ];

        $this->dependencyMocks['validationHandler']->method('validateApi')
            ->willReturn(true);

        $this->dependencyMocks['paymentConfigProvider']
            ->method('updateWithQuoteData')
            ->with(
                $this->quote,
                $this->dependencyMocks['paymentConfigProvider']->getPaymentConfig($this->quote->getStore())
            )
            ->willReturn($expected);

        $result = $this->kpConfigProvider->getConfig();
        static::assertEquals($expected, $result);
    }

    protected function setUp(): void
    {
        $this->kpConfigProvider = parent::setUpMocks(KpConfigProvider::class);

        $store = $this->mockFactory->create(Store::class);
        $this->quote = $this->mockFactory->create(Quote::class);
        $this->quote->method('getStore')
            ->willReturn($store);
        $this->dependencyMocks['session']->method('getQuote')
            ->willReturn($this->quote);

        $this->dependencyMocks['paymentConfigProvider']
            ->method('getPaymentConfig')
            ->willReturn(
                [
                    'payment' => [
                        'klarna_kp' => [
                            'client_token'        => null,
                            'message'             => null,
                            'success'             => 0,
                            'available_methods'   => [
                                'type'      => 'klarna_kp',
                                'component' => 'Klarna_Kp/js/view/payments/kp'
                            ]
                        ]
                    ]
                ]
            );
    }
}
