<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Plugin\Payment\Helper;

use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Plugin\Payment\Helper\DataPlugin;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\CartInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Store\Model\Store;

/**
 * @coversDefaultClass \Klarna\Kp\Plugin\Payment\Helper\DataPlugin
 */
class DataPluginTest extends TestCase
{
    /**
     * @var DataPlugin|MockObject
     */
    private $subject;

    /**
     * @var MockObject | Data
     */
    private $dataObjectMock;

    public function testItShouldReturnUnchangedResultWhenKlarnaSessionRequestCannotBeSent(): void
    {
        $result = $this->subject->afterGetPaymentMethods($this->dataObjectMock, ['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $result);
    }

    public function testItShouldReturnUnchangedResultWhenQuoteProviderReturnsNoQuote(): void
    {
        $this->setKlarnaSessionCanSendRequest();

        $result = $this->subject->afterGetPaymentMethods($this->dataObjectMock, ['foo' => 'bar2']);

        $this->assertSame(['foo' => 'bar2'], $result);
    }

    public function testItShouldReturnUnchangedResultWhenQuoteIsNotActive(): void
    {
        $this->setKlarnaSessionCanSendRequest();

        $quote = $this->mockFactory->create(CartInterface::class);
        $quote->method('getIsActive')->willReturn(false);
        $this->dependencyMocks['quoteProvider']
            ->method('getQuote')
            ->willReturn($quote);

        $result = $this->subject->afterGetPaymentMethods($this->dataObjectMock, ['foo' => 'bar3']);

        $this->assertSame(['foo' => 'bar3'], $result);
    }

    public function testItShouldReturnUnchangedResultWhenKlarnaQuoteIsNotActive(): void
    {
        $this->setKlarnaSessionCanSendRequest();
        $this->setQuoteProviderWillReturnActiveQuote();

        $klarnaQuote = $this->mockFactory->create(QuoteInterface::class);
        $klarnaQuote->method('isActive')->willReturn(false);
        $this->dependencyMocks['action']
            ->method('sendRequest')
            ->willReturn($klarnaQuote);

        $result = $this->subject->afterGetPaymentMethods($this->dataObjectMock, ['foo' => 'bar3']);

        $this->assertSame(['foo' => 'bar3'], $result);
    }

    public function testItShouldReturnUnchangedResultWhenKlarnaOrderUpdateFailsAsResponseFailed(): void
    {
        $this->setKlarnaSessionCanSendRequest();
        $this->setQuoteProviderWillReturnActiveQuote();
        $this->setQuoteRepositoryWillReturnKlarnaQuoteWithSessionId('the_k_session');

        $this->dependencyMocks['action']
            ->method('sendRequest');

        $result = $this->subject->afterGetPaymentMethods($this->dataObjectMock, ['foo' => 'bar4']);

        $this->assertSame(['foo' => 'bar4'], $result);
    }

    public function testItShouldReturnUnchangedResultWhenPaymentMethodListIsEmpty(): void
    {
        $this->setKlarnaSessionCanSendRequest();
        $this->setQuoteProviderWillReturnActiveQuote();
        $this->setQuoteRepositoryWillReturnKlarnaQuoteWithSessionId('the_k_session2');

        $this->dependencyMocks['action']
            ->method('sendRequest');

        $result = $this->subject->afterGetPaymentMethods($this->dataObjectMock, ['foo' => 'bar4']);

        $this->assertSame(['foo' => 'bar4'], $result);
    }

    public function testItShouldCallTheCallableWhenPaymentCodeDoesNotContainKlarnaWord(): void
    {
        $callable = static function (string $code) {
            return $code . '_is_ok';
        };

        $result = $this->subject->aroundGetMethodInstance($this->dataObjectMock, $callable, 'foo_bar');

        $this->assertSame('foo_bar_is_ok', $result);
    }

    public function testItShouldCallTheCallableWhenPaymentCodeContainsKlarnaKco(): void
    {
        $callable = static function (string $code) {
            return $code . '_is_ok';
        };

        $result = $this->subject->aroundGetMethodInstance($this->dataObjectMock, $callable, 'klarna_kco');

        $this->assertSame('klarna_kco_is_ok', $result);
    }

    public function testItShouldCallPaymentMethodListGetPaymentMethodWhenPaymentMethodIsKlarnaSomething(): void
    {
        $methodMock = $this->mockFactory->create(MethodInterface::class);
        $this->dependencyMocks['paymentMethodProvider']
            ->method('createPaymentMethod')
            ->with('klarna_foo')
            ->willReturn($methodMock);

        $result = $this->subject->aroundGetMethodInstance($this->dataObjectMock, fn ($x) => $x, 'klarna_foo');

        $this->assertInstanceOf(MethodInterface::class, $result);
    }

    /**
     * Basic setup for test
     */
    protected function setUp(): void
    {
        $this->subject = parent::setUpMocks(DataPlugin::class);
        $this->dataObjectMock  = $this->mockFactory->create(Data::class);
    }

    private function setKlarnaSessionCanSendRequest(): void
    {
        $this->dependencyMocks['apiValidation']->method('sendApiRequestAllowed')
            ->willReturn(true);
    }

    private function setQuoteProviderWillReturnActiveQuote(): void
    {
        $quote = $this->mockFactory->create(CartInterface::class);
        $quote->method('getIsActive')->willReturn(true);
        $this->dependencyMocks['quoteProvider']
            ->method('getQuote')
            ->willReturn($quote);
    }

    private function setQuoteRepositoryWillReturnKlarnaQuoteWithSessionId(string $sessionId): void
    {
        $klarnaQuote = $this->mockFactory->create(QuoteInterface::class);
        $klarnaQuote->method('getSessionId')->willReturn($sessionId);
        $this->dependencyMocks['quoteRepository']
            ->method('getActiveByQuote')
            ->willReturn($klarnaQuote);
    }
}
