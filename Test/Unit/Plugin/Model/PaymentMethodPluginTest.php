<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Plugin\Model;

use Klarna\Kp\Plugin\Model\PaymentMethodPlugin;
use Magento\AdvancedSalesRule\Model\Rule\Condition\FilterTextGenerator\Address\PaymentMethod;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\MockObject\MockObject;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Store\Model\Store;
use Magento\Quote\Model\Quote as MagentoQuote;

/**
 * @coversDefaultClass \Klarna\Kp\Plugin\Model\PaymentMethodPlugin
 */
class PaymentMethodPluginTest extends TestCase
{
    /**
     * @var PaymentMethodPlugin
     */
    private $model;
    /**
     * @var PaymentMethod|MockObject
     */
    private $subject;

    public function testAfterGenerateFilterTextNoQuoteIdInSessionImpliesReturnsUnchangedInput(): void
    {
        $expected = ['input'];
        $result = $this->model->afterGenerateFilterText($this->subject, $expected);
        static::assertEquals($expected, $result);
    }

    public function testAfterGenerateFilterTextNoKlarnaQuoteExistsImpliesReturnsUnchangedInput(): void
    {
        $this->dependencyMocks['checkoutSession']->method('getQuoteId')
            ->willReturn('1');
        $this->dependencyMocks['klarnaQuoteRepository']->method('existsEntryByQuoteId')
            ->willReturn(false);

        $expected = ['input'];
        $result = $this->model->afterGenerateFilterText($this->subject, $expected);
        static::assertEquals($expected, $result);
    }

    public function testAfterGenerateFilterTextKlarnaDisabledReturnUnchangedInput(): void
    {
        $this->dependencyMocks['checkoutSession']->method('getQuoteId')
            ->willReturn('1');
        $this->dependencyMocks['klarnaQuoteRepository']->method('existsEntryByQuoteId')
            ->willReturn(true);

        $this->dependencyMocks['apiValidation']
            ->method('isKpEnabled')
            ->willReturn(false);

        $result = $this->model->afterGenerateFilterText($this->subject, ['input']);
        static::assertSame(['input'], $result);
    }

    public function testAfterGenerateFilterTextNoPaymentMethodInArgumentReturnsUnchangedInput(): void
    {
        $this->dependencyMocks['checkoutSession']->method('getQuoteId')
            ->willReturn('1');
        $this->dependencyMocks['klarnaQuoteRepository']->method('existsEntryByQuoteId')
            ->willReturn(true);

        $this->dependencyMocks['apiValidation']
            ->method('isKpEnabled')
            ->willReturn(true);

        $this->dependencyMocks['klarnaSession']->method('getPaymentMethods')
            ->willReturn(['klarna_x']);

        $result = $this->model->afterGenerateFilterText($this->subject, ['some:prefix:value']);
        static::assertSame(['some:prefix:value'], $result);
    }

    public function testAfterGenerateFilterTextNonKlarnaPaymentMethodInArgumentReturnsUnchangedInput(): void
    {
        $this->dependencyMocks['checkoutSession']->method('getQuoteId')
            ->willReturn('1');
        $this->dependencyMocks['klarnaQuoteRepository']->method('existsEntryByQuoteId')
            ->willReturn(true);

        $this->dependencyMocks['apiValidation']
            ->method('isKpEnabled')
            ->willReturn(true);

        $this->dependencyMocks['klarnaSession']->method('getPaymentMethods')
            ->willReturn(['klarna_x']);

        $result = $this->model->afterGenerateFilterText($this->subject, [
            'quote_address:payment_method:other_payment_provider_method'
        ]);
        static::assertSame(['quote_address:payment_method:other_payment_provider_method'], $result);
    }

    public function testAfterGenerateFilterTextKlarnaPaymentMethodInArgumentReturnsReplacedInput(): void
    {
        $this->dependencyMocks['checkoutSession']->method('getQuoteId')
            ->willReturn('1');
        $this->dependencyMocks['klarnaQuoteRepository']->method('existsEntryByQuoteId')
            ->willReturn(true);

        $this->dependencyMocks['apiValidation']
            ->method('isKpEnabled')
            ->willReturn(true);

        $this->dependencyMocks['klarnaSession']->method('getPaymentMethods')
            ->willReturn(['x']);

        $result = $this->model->afterGenerateFilterText($this->subject, [
            'quote_address:payment_method:x'
        ]);
        static::assertSame([
            'quote_address:payment_method:klarna_kp'
        ], $result);
    }

    /**
     * Basic setup for test
     */
    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(PaymentMethodPlugin::class);

        $this->subject = $this->mockFactory->create(DataObject::class);

        $store = $this->mockFactory->create(Store::class);
        $this->dependencyMocks['storeManager']->method('getStore')
            ->willReturn($store);
    }
}
