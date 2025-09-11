<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\PaymentMethods;

use Klarna\Kp\Model\Initialization\Validator;
use Klarna\Kp\Model\Quote as KlarnaQuote;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Quote\Model\Quote as MagentoQuote;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Initialization\Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * @var Validator
     */
    private Validator $validator;
    /**
     * @var MagentoQuote
     */
    private MagentoQuote $magentoQuote;

    public function testIsKlarnaSessionRunningNoSessionIdSet(): void
    {
        $klarnaQuote = $this->mockFactory->create(KlarnaQuote::class);

        $this->dependencyMocks['klarnaQuoteRepository']
            ->expects(static::once())
            ->method('markInactive');

        $klarnaQuote->method('isKecSession')
            ->willReturn(false);
        $klarnaQuote->method('isActive')
            ->willReturn(true);


        static::assertFalse($this->validator->isKlarnaSessionRunning($klarnaQuote));
    }

    public function testIsKlarnaSessionExistsAndSessionIdSet(): void
    {
        $klarnaQuote = $this->mockFactory->create(KlarnaQuote::class);

        $klarnaQuote->method('getSessionId')
            ->willReturn('1');
        $this->dependencyMocks['klarnaQuoteRepository']
            ->expects(static::never())
            ->method('markInactive');
        $klarnaQuote->method('isKecSession')
            ->willReturn(false);
        $klarnaQuote->method('isActive')
            ->willReturn(true);

        static::assertTrue($this->validator->isKlarnaSessionRunning($klarnaQuote));
    }

    public function testIsKlarnaSessionRunningEntryExistsAndItsKecSession(): void
    {
        $klarnaQuote = $this->mockFactory->create(KlarnaQuote::class);

        $klarnaQuote
            ->method('isKecSession')
            ->willReturn(true);
        $this->dependencyMocks['klarnaQuoteRepository']
            ->expects(static::never())
            ->method('markInactive');

        static::assertTrue($this->validator->isKlarnaSessionRunning($klarnaQuote));
    }

    protected function setUp(): void
    {
        $this->validator = parent::setUpMocks(Validator::class);
        $this->magentoQuote = $this->mockFactory->create(MagentoQuote::class);
    }
}
