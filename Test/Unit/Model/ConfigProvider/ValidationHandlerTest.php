<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Unit\Model\ConfigProvider;

use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Kp\Model\ConfigProvider\ValidationHandler;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\Store;

class ValidationHandlerTest extends TestCase
{
    /**
     * @var ValidationHandler|MockObject
     */
    private ValidationHandler $validationHandler;

    public function testGetConfigKpNotEnabled(): void
    {
        $this->dependencyMocks['apiValidation']
            ->method('getFailedValidationHistory')
            ->willReturn(['aaa', 'bbb']);

        $result = $this->validationHandler->getValidationMessage();
        static::assertEquals(
            'Klarna Payments will not show up. Reason: aaa, bbb',
            $result
        );
    }

    public function testValidateApiSuccess(): void
    {
        $quote = $this->mockFactory->create(Quote::class);

        $this->dependencyMocks['apiValidation']
            ->expects(static::once())
            ->method('sendApiRequestAllowed')
            ->with($quote)
            ->willReturn(true);

        $result = $this->validationHandler->validateApi($quote);
        static::assertTrue($result);
    }

    public function testIsKpEnabledSuccess(): void
    {
        $store = $this->mockFactory->create(Store::class);

        $this->dependencyMocks['apiValidation']
            ->expects(static::once())
            ->method('isKpEnabled')
            ->with($store)
            ->willReturn(true);

        $result = $this->validationHandler->isKpEnabled($store);
        static::assertTrue($result);
    }

    protected function setUp(): void
    {
        $this->validationHandler = parent::setUpMocks(ValidationHandler::class);
    }
}