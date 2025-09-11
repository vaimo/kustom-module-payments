<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Unit\Plugin\Model;

use Klarna\Kp\Model\PaymentMethods\GenericPaymentKey;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Magento\Quote\Model\Quote as MagentoQuote;
use Klarna\Base\Test\Unit\Mock\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Klarna\Kp\Model\PaymentMethods\GenericPaymentKey
 */
class GenericPaymentKeyTest extends TestCase
{
    /**
     * @var GenericPaymentKey
     */
    private GenericPaymentKey $model;
    /**
     * @var Quote|MockObject
     */
    private $magentoQuote;
    /**
     * @var Store|MockObject
     */
    private $store;
    /**
     * @var string|array
     */
    private string|array $input;

    public function testGetGenericKpKeyNoQuoteIdInSessionImpliesReturningOriginalValue(): void
    {
        $this->dependencyMocks['checkoutSession']
            ->method('getQuoteId')
            ->willReturn(null);

        $result = $this->model->getGenericKpKey($this->input);
        static::assertSame($this->input, $result);
    }

    public function testGetGenericKpKeyReturnsReplacedInput(): void
    {
        $this->dependencyMocks['checkoutSession']
            ->method('getQuoteId')
            ->willReturn('1');
        $this->dependencyMocks['apiValidation']
            ->method('isKpEnabled')
            ->willReturn(true);
        $this->dependencyMocks['quoteRepository']
            ->method('existsEntryByQuoteId')
            ->willReturn(true);
        $this->dependencyMocks['klarnaSession']
            ->method('getPaymentMethodInformation')
            ->willReturn([['x']]);
        $this->dependencyMocks['storeManager']->method('getStore')
            ->willReturn($this->store);
        $this->dependencyMocks['magentoQuoteRepository']
            ->method('get')
            ->willReturn($this->magentoQuote);

        $this->input = "klarna_x";

        $result = $this->model->getGenericKpKey($this->input);
        static::assertSame("klarna_kp", $result);
    }

    public function testGetGenericKpKeyQuoteDoesNotExistImpliesReturnsInput(): void
    {
        $this->dependencyMocks['checkoutSession']
            ->method('getQuoteId')
            ->willReturn('1');
        $this->magentoQuote
            ->method('getStore')
            ->willReturn($this->store);
        $this->dependencyMocks['quoteRepository']
            ->method('existsEntryByQuoteId')
            ->willReturn(false);

        $this->input = "klarna_x";

        $result = $this->model->getGenericKpKey($this->input);
        static::assertSame($this->input, $result);
    }

    public function testGetGenericKpKeyKpNotEnabledReturnsInput(): void
    {
        $this->dependencyMocks['checkoutSession']
            ->method('getQuoteId')
            ->willReturn('1');
        $this->dependencyMocks['apiValidation']
            ->method('isKpEnabled')
            ->willReturn(false);
        $this->dependencyMocks['quoteRepository']
            ->method('existsEntryByQuoteId')
            ->willReturn(true);
        $this->dependencyMocks['storeManager']->method('getStore')
            ->willReturn($this->store);
        $this->dependencyMocks['magentoQuoteRepository']
            ->method('get')
            ->willReturn($this->magentoQuote);

        $this->input = "klarna_x";

        $result = $this->model->getGenericKpKey($this->input);
        static::assertSame($this->input, $result);
    }

    public function testGenericKpKeyInputIsArray(): void
    {
        $this->dependencyMocks['checkoutSession']
            ->method('getQuoteId')
            ->willReturn('1');
        $this->dependencyMocks['apiValidation']
            ->method('isKpEnabled')
            ->willReturn(false);
        $this->dependencyMocks['quoteRepository']
            ->method('existsEntryByQuoteId')
            ->willReturn(true);
        $this->dependencyMocks['storeManager']->method('getStore')
            ->willReturn($this->store);
        $this->dependencyMocks['magentoQuoteRepository']
            ->method('get')
            ->willReturn($this->magentoQuote);

        $this->input = ["klarna_x", "klarna_y", "klarna_z"];

        $result = $this->model->getGenericKpKey($this->input);
        static::assertSame($this->input, $result);
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(GenericPaymentKey::class);

        $this->input = "klarna_x";
        $this->store = $this->mockFactory->create(Store::class);
        $this->magentoQuote = $this->mockFactory->create(MagentoQuote::class);
    }
}
