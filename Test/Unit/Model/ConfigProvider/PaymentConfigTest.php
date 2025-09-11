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
use Klarna\Kp\Model\ConfigProvider\PaymentConfig;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\Store;
use Klarna\Kp\Model\Quote as KlarnaQuote;

class PaymentConfigTest extends TestCase
{
    /**
     * @var PaymentConfig|MockObject
     */
    private PaymentConfig $paymentConfig;

    public function testGetConfigApiRequestWontUpdateKpConfiguration(): void
    {
        $quote = $this->mockFactory->create(Quote::class);
        $store = $this->mockFactory->create(Store::class);
        $klarnaQuote = $this->mockFactory->create(KlarnaQuote::class);

        $klarnaQuote
            ->method('isActive')
            ->willReturn(false);

        $this->dependencyMocks['action']
            ->method('sendRequest')
            ->with($quote)
            ->willReturn($klarnaQuote);

        $this->dependencyMocks['action']
            ->method('sendRequest')
            ->willReturn($klarnaQuote);

        $result = $this->paymentConfig->updateWithQuoteData(
            $quote,
            $this->paymentConfig->getPaymentConfig($store)
        );

        self::assertArrayNotHasKey('is_kec_session', $result['payment']['klarna_kp']);
        self::assertEquals($result['payment']['klarna_kp']['success'], 0);
    }

    public function testGetConfigReturningConfiguration(): void
    {
        $quote = $this->mockFactory->create(Quote::class);
        $store = $this->mockFactory->create(Store::class);
        $klarnaQuote = $this->mockFactory->create(KlarnaQuote::class);

        $expected = [
            'any_key' => 'Any value',
            [
                'another_any_key' => 'Another any value'
            ]
        ];

        $klarnaQuote
            ->method('isActive')
            ->willReturn(true);

        $klarnaQuote
            ->method('getPaymentMethodInfo')
            ->willReturn(['klarna_pay_later']);

        $this->dependencyMocks['action']
            ->method('sendRequest')
            ->with($quote)
            ->willReturn($klarnaQuote);

        $this->dependencyMocks['paymentMethodProvider']
            ->method('getAvailablePaymentMethods')
            ->willReturn($expected);

        $result = $this->paymentConfig->updateWithQuoteData(
            $quote,
            $this->paymentConfig->getPaymentConfig($store)
        );
        static::assertEquals($expected, $result);
    }

    protected function setUp(): void
    {
        $this->paymentConfig = parent::setUpMocks(PaymentConfig::class);
    }
}