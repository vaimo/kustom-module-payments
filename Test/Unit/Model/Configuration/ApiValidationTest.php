<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Configuration;

use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Store\Model\Store;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Configuration\ApiValidation
 */
class ApiValidationTest extends TestCase
{
    /**
     * @var ApiValidation|object
     */
    private ApiValidation $apiValidation;
    /**
     * @var Store
     */
    private Store $store;
    /**
     * @var Quote
     */
    private Quote $quote;

    public function testIsKpEnabledSettingSetToTrue(): void
    {
        $this->dependencyMocks['paymentConfig']->method('IsEnabled')
            ->willReturn(true);
        static::assertTrue($this->apiValidation->isKpEnabled($this->store));
    }

    public function testIsKpEnabledSettingSetToFalse(): void
    {
        $validationHistory = ['Klarna Payments in not enabled'];

        $this->dependencyMocks['paymentConfig']->method('IsEnabled')
                ->willReturn(false);
        static::assertFalse($this->apiValidation->isKpEnabled($this->store));
        static::assertEquals($validationHistory, $this->apiValidation->getFailedValidationHistory());
    }

    public function testSendApiRequestAllowedInvalidKpApiSettings(): void
    {
        $validationHistory = ['Klarna Payments in not enabled'];

        $expectKpMisconfigured = false;
        $canKpRequestBeSent = $this->apiValidation->sendApiRequestAllowed($this->quote);
        static::assertFalse($expectKpMisconfigured, $canKpRequestBeSent);
        static::assertEquals($validationHistory, $this->apiValidation->getFailedValidationHistory());
    }

    public function testSendApiRequestCountryIsAllowed(): void
    {
        $this->dependencyMocks['paymentConfig']->method('IsEnabled')
            ->willReturn(true);
        $this->dependencyMocks['generalConfig']->method('isCountryAllowed')
            ->willReturn(true);

        static::assertTrue($this->apiValidation->sendApiRequestAllowed($this->quote));
    }

    public function testSendApiRequestAllowedTargetCountryIsNotAllowed(): void
    {
        $this->dependencyMocks['paymentConfig']->method('IsEnabled')
            ->willReturn(true);

        $validationHistory = ['Klarna Payments is not allowed to be shown for quote id: 1'];
        static::assertFalse($this->apiValidation->sendApiRequestAllowed($this->quote));
        static::assertEquals($validationHistory, $this->apiValidation->getFailedValidationHistory());
    }

    protected function setUp(): void
    {
        $this->apiValidation = parent::setUpMocks(ApiValidation::class);

        $this->store = $this->mockFactory->create(Store::class);
        $this->quote = $this->mockFactory->create(Quote::class);
        $this->quote->method('getStore')
            ->willReturn($this->store);

        $address = $this->mockFactory->create(Address::class);
        $address->method('getCountryId')
            ->willReturn('DE');
        $this->quote->method('getShippingAddress')
            ->willReturn($address);
        $this->quote->method('getId')
            ->willReturn(1);
    }
}
