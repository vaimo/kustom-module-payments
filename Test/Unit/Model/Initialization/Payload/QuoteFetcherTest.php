<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Initialization\Payload;

use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Kp\Model\Initialization\Payload\QuoteFetcher;
use Magento\Quote\Model\Quote;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Initialization\Payload\RequestFetcher
 */
class QuoteFetcherTest extends TestCase
{
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var QuoteFetcher
     */
    private QuoteFetcher $quoteFetcher;

    public function testGetMagentoQuoteUseExistingQuote(): void
    {
        $this->quote->method('getId')
            ->willReturn('1');
        $this->dependencyMocks['quoteFiller']->expects(static::once())
            ->method('fillExistingMagentoQuote')
            ->willReturn($this->quote);

        $input = [
            'additional_input' => [
                'use_existing_quote' => '1'
            ]
        ];
        static::assertSame($this->quote, $this->quoteFetcher->getMagentoQuote($input));
    }

    public function testGetMagentoQuoteCreateNewQuote(): void
    {
        $this->quote->method('getId')
            ->willReturn(null);
        $this->dependencyMocks['quoteFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->quote);
        $this->dependencyMocks['quoteFiller']->method('fillEmptyMagentoQuote')
            ->willReturn($this->quote);

        $input = [
            'additional_input' => [
                'use_existing_quote' => '0'
            ]
        ];
        static::assertSame($this->quote, $this->quoteFetcher->getMagentoQuote($input));
    }

    protected function setUp(): void
    {
        $this->quoteFetcher = parent::setUpMocks(QuoteFetcher::class);

        $this->quote = $this->mockFactory->create(Quote::class);
        $this->dependencyMocks['session']->method('getQuote')
            ->willReturn($this->quote);
        $this->dependencyMocks['quoteFactory']->method('create')
            ->willReturn($this->quote);
    }
}