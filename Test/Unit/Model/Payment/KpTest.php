<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Payment;

use Magento\Store\Model\Store;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Quote\Model\Quote;

class KpTest extends TestCase
{
    /**
     * @var Kp
     */
    private Kp $kp;
    /**
     * @var Quote
     */
    private Quote $magentoQuote;

    public function testIsActiveKpIsEnabled(): void
    {
        $store = $this->mockFactory->create(Store::class);
        $this->dependencyMocks['storeManager']->method('getStore')
            ->willReturn($store);
        $this->dependencyMocks['apiValidation']->method('isKpEnabled')
            ->willReturn(true);
        static::assertTrue($this->kp->isActive('1'));
    }

    public function testIsActiveKpIsDisabled(): void
    {
        $store = $this->mockFactory->create(Store::class);
        $this->dependencyMocks['storeManager']->method('getStore')
            ->willReturn($store);
        $this->dependencyMocks['apiValidation']->method('isKpEnabled')
            ->willReturn(false);
        static::assertFalse($this->kp->isActive('1'));
    }

    public function testIsAvailableAdapterReturnsFalse(): void
    {
        $this->dependencyMocks['adapter']->method('isAvailable')
            ->willReturn(false);

        static::assertFalse($this->kp->isAvailable($this->magentoQuote));
    }

    public function testIsAvailableIsKecSessionReturnsTrue(): void
    {
        $this->dependencyMocks['adapter']->method('isAvailable')
            ->willReturn(true);
        $this->kp->setCode(Kp::ONE_KLARNA_PAYMENT_METHOD_CODE_WITH_PREFIX);

        static::assertTrue($this->kp->isAvailable($this->magentoQuote));
    }

    public function testIsAvailableProviderReturnsTrue(): void
    {
        $this->dependencyMocks['adapter']->method('isAvailable')
            ->willReturn(true);
        $this->dependencyMocks['paymentMethodProvider']->method('existMethodInAvailableMethodList')
            ->willReturn(true);

        static::assertTrue($this->kp->isAvailable($this->magentoQuote));
    }

    public function testIsAvailableProviderReturnsFalse(): void
    {
        $this->dependencyMocks['adapter']->method('isAvailable')
            ->willReturn(true);
        $this->dependencyMocks['paymentMethodProvider']->method('existMethodInAvailableMethodList')
            ->willReturn(false);

        static::assertFalse($this->kp->isAvailable($this->magentoQuote));
    }

    protected function setUp(): void
    {
        $this->kp = parent::setUpMocks(Kp::class);
        $this->magentoQuote = $this->mockFactory->create(Quote::class);
    }
}
