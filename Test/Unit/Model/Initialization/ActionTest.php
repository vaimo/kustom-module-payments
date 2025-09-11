<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Initialization;

use Klarna\Kp\Model\Initialization\Action;
use Magento\Framework\Exception\NoSuchEntityException;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Quote\Model\Quote as MagentoQuote;
use Klarna\Kp\Model\Quote;
use Magento\Store\Model\Store;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Initialization\Action
 */
class ActionTest extends TestCase
{
    /**
     * @var Action
     */
    private Action $action;
    /**
     * @var MagentoQuote
     */
    private MagentoQuote $magentoQuote;
    /**
     * @var Quote|\PHPUnit\Framework\MockObject\MockObject
     */
    private Quote $klarnaQuote;

    public function testSendRequestNoKlarnaSessionExists(): void
    {
        $newKlarnaQuote = $this->mockFactory->create(Quote::class);
        $this->dependencyMocks['klarnaQuoteRepository']
            ->method('getActiveByQuote')
            ->willThrowException(new NoSuchEntityException(__('')));

        $this->dependencyMocks['klarnaQuoteFactory']
            ->method('create')
            ->willReturn($newKlarnaQuote);

        $this->dependencyMocks['klarnaQuoteHandler']
            ->method('createKlarnaQuote')
            ->willReturn($this->klarnaQuote);

        $result = $this->action->sendRequest($this->magentoQuote);
        static::assertEquals($this->klarnaQuote, $result);
    }

    public function testSendRequestWithDisabledAuthorizationToken(): void
    {
        $disableAuthorizationCallback = true;

        $this->dependencyMocks['klarnaQuoteHandler']
            ->method('createKlarnaQuote')
            ->with($this->magentoQuote, $disableAuthorizationCallback)
            ->willReturn($this->klarnaQuote);

        $result = $this->action->sendRequest($this->magentoQuote, ['disableAuthorizationCallback' => $disableAuthorizationCallback]);
        static::assertSame($this->klarnaQuote, $result);
    }

    public function testSendRequestWithEnabledAuthorizationToken(): void
    {
        $disableAuthorizationCallback = false;

        $this->dependencyMocks['klarnaQuoteHandler']
            ->method('createKlarnaQuote')
            ->with($this->magentoQuote, $disableAuthorizationCallback)
            ->willReturn($this->klarnaQuote);

        $result = $this->action->sendRequest($this->magentoQuote, ['disableAuthorizationCallback' => $disableAuthorizationCallback]);
        static::assertSame($this->klarnaQuote, $result);
    }

    public function testSendRequestWithEnabledAuthorizationTokenIfNotOptionPassed(): void
    {
        $disableAuthorizationCallback = false;

        $this->dependencyMocks['klarnaQuoteHandler']
            ->method('createKlarnaQuote')
            ->with($this->magentoQuote, $disableAuthorizationCallback)
            ->willReturn($this->klarnaQuote);

        $result = $this->action->sendRequest($this->magentoQuote, []);
        static::assertSame($this->klarnaQuote, $result);
    }

    public function testSendRequestUpdatingTheSession(): void
    {
        $this->klarnaQuote->method('isActive')
            ->willReturn(true);

        $this->dependencyMocks['klarnaQuoteRepository']
            ->method('getActiveByQuote')
            ->willReturn($this->klarnaQuote);

        $this->dependencyMocks['validator']
            ->method('isKlarnaSessionRunning')
            ->willReturn(true);

        $this->dependencyMocks['klarnaQuoteHandler']
            ->method('updateKlarnaQuote')
            ->willReturn($this->klarnaQuote);

        $result = $this->action->sendRequest($this->magentoQuote);
        static::assertEquals($this->klarnaQuote, $result);
    }

    public function testSendRequestItsPspMerchantImpliesNoRequestSent(): void
    {
        $this->dependencyMocks['pluginsApiValidator']
            ->method('isPspMerchantByStore')
            ->willReturn(true);
        $this->dependencyMocks['klarnaQuoteRepository']
            ->method('getActiveByQuote')
            ->willReturn($this->klarnaQuote);

        $this->dependencyMocks['klarnaQuoteHandler']->expects($this->never())
            ->method('createKlarnaQuote');
        $this->dependencyMocks['klarnaQuoteHandler']->expects($this->never())
            ->method('updateKlarnaQuote');

        $result = $this->action->sendRequest($this->magentoQuote);
        static::assertEquals($this->klarnaQuote, $result);
    }

    protected function setUp(): void
    {
        $this->action = parent::setUpMocks(Action::class);

        $this->magentoQuote = $this->mockFactory->create(MagentoQuote::class);
        $this->klarnaQuote = $this->mockFactory->create(Quote::class);

        $this->klarnaQuote->method('getSessionId')
            ->willReturn('1');

        $store = $this->mockFactory->create(Store::class);
        $store->method('getCode')
            ->willReturn('code');
        $store->method('getId')
            ->willReturn(1);
        $this->magentoQuote->method('getStore')
            ->willReturn($store);
    }
}
