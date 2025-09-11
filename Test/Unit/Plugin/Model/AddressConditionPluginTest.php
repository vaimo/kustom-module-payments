<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Unit\Plugin\Model;

use Klarna\Kp\Plugin\Model\AddressConditionPlugin;
use Magento\SalesRule\Model\Rule\Condition\Address;
use PHPUnit\Framework\MockObject\MockObject;
use Klarna\Base\Test\Unit\Mock\TestCase;

/**
 * @coversDefaultClass \Klarna\Kp\Plugin\Model\AddressConditionPlugin
 */
class AddressConditionPluginTest extends TestCase
{
    /**
     * @var AddressConditionPlugin
     */
    private $model;
    /**
     * @var Address|MockObject
     */
    private $address;

    /**
     * @covers ::beforeValidateAttribute()
     */
    public function testBeforeValidateAttributeInputIsNull(): void
    {
        $input = null;

        $result = $this->model->beforeValidateAttribute($this->address, $input);
        static::assertSame(null, $result);
    }

    /**
     * Basic setup for test
     */
    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(AddressConditionPlugin::class);
        $this->address = $this->mockFactory->create(Address::class);
    }
}
