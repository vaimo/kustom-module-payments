<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder\Nodes;

use Klarna\Kp\Model\Api\Builder\Nodes\MerchantReferences;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Quote\Model\Quote;
use Klarna\Kp\Model\Api\Request\Builder;
use Magento\Framework\DataObject;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\MerchantReferences
 */
class MerchantReferencesTest extends TestCase
{
    /**
     * @var MerchantReferences
     */
    private MerchantReferences $model;
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var Builder
     */
    private Builder $requestBuilder;
    /**
     * @var DataObject
     */
    private DataObject $dataObject;

    public function testAddToRequestSettingTheReferencesToTheRequest(): void
    {
        $this->requestBuilder->expects(static::once())
            ->method('setMerchantReferences')
            ->with($this->dataObject);

        $this->dependencyMocks['dataObjectFactory']->method('create')
            ->willReturn($this->dataObject);

        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(MerchantReferences::class);

        $this->quote = $this->mockFactory->create(Quote::class);
        $this->requestBuilder = $this->mockFactory->create(Builder::class);
        $this->dataObject = $this->mockFactory->create(DataObject::class);
    }
}
