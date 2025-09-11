<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder\Nodes;

use Klarna\Kp\Model\Api\Builder\Nodes\Miscellaneous;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Quote\Model\Quote;
use Klarna\Kp\Model\Api\Request\Builder;
use Magento\Store\Model\Store;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\Miscellaneous
 */
class MiscellaneousTest extends TestCase
{
    /**
     * @var Miscellaneous
     */
    private Miscellaneous $model;
    /**
     * @var Builder
     */
    private Builder $requestBuilder;
    /**
     * @var Quote
     */
    private Quote $quote;

    public function testAddToRequestSettingPurchaseCurrency(): void
    {
        $targetCurrency = 'EUR';
        $this->quote->method('getBaseCurrencyCode')
            ->willReturn($targetCurrency);
        $this->requestBuilder->expects(static::once())
            ->method('setPurchaseCurrency')
            ->with($targetCurrency);
        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    public function testAddToRequestSettingLocale(): void
    {
        $this->dependencyMocks['magentoToKlarnaLocaleMapper']->method('getLocale')
            ->willReturn('de_DE');
        $this->requestBuilder->expects(static::once())
            ->method('setLocale')
            ->with('de_DE');
        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(Miscellaneous::class);

        $this->requestBuilder = $this->mockFactory->create(Builder::class);
        $this->quote = $this->mockFactory->create(Quote::class, ['getStore'], ['getBaseCurrencyCode']);

        $store = $this->mockFactory->create(Store::class);
        $this->quote->method('getStore')
            ->willReturn($store);

    }
}
