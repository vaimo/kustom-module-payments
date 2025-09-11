<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder\Customer;

use Klarna\Kp\Model\Api\Builder\Customer\Generator;
use Klarna\Siwk\Model\Authentication\Token\Container;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\Store;
use Klarna\Base\Test\Unit\Mock\TestCase;

/**
 * @coversDefaultClass  \Klarna\Kp\Model\Api\Builder\Customer\Generator
 */
class GeneratorTest extends TestCase
{
    /**
     * @var Generator
     */
    private Generator $model;
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var Address
     */
    private Address $address;
    /**
     * @var Container
     */
    private Container $container;

    public function testGetBasicDataReturnsTypeValue(): void
    {
        $expected = 'company';
        $this->dependencyMocks['typeResolver']->method('getData')
            ->willReturn($expected);

        $result = $this->model->getBasicData($this->quote);
        static::assertEquals($expected, $result['type']);
    }

    public function testGetWithPrefilledDataReturnsTypeValue(): void
    {
        $expected = 'company';
        $this->dependencyMocks['typeResolver']->method('getData')
            ->willReturn($expected);

        $result = $this->model->getWithPrefilledData($this->quote);
        static::assertEquals($expected, $result['type']);
    }

    public function testGetWithPrefilledDataCustomerLoggedInCustomerAndNoDobValueGiven(): void
    {
        $this->quote->method('getCustomerIsGuest')
            ->willReturn(false);
        $this->quote->method('getCustomerDob')
            ->willReturn('');

        $result = $this->model->getWithPrefilledData($this->quote);
        static::assertTrue(!isset($result['date_of_birth']));
    }

    public function testGetWithPrefilledDataGuestCustomerAndNoDobValueGiven(): void
    {
        $this->quote->method('getCustomerIsGuest')
            ->willReturn(true);
        $this->quote->method('getCustomerDob')
            ->willReturn('');

        $result = $this->model->getWithPrefilledData($this->quote);
        static::assertTrue(!isset($result['date_of_birth']));
    }

    public function testGetWithPrefilledDataGuestCustomerButDobValueGiven(): void
    {
        $this->quote->method('getCustomerIsGuest')
            ->willReturn(false);
        $this->quote->method('getCustomerDob')
            ->willReturn('');

        $result = $this->model->getWithPrefilledData($this->quote);
        static::assertTrue(!isset($result['date_of_birth']));
    }

    public function testGetWithPrefilledDataLoggedInCustomerAndDobValueGiven(): void
    {
        $this->quote->method('getCustomerIsGuest')
            ->willReturn(false);
        $this->quote->method('getCustomerDob')
            ->willReturn('1987-11-11');
        $this->dependencyMocks['dateTime']->method('date')
            ->with('Y-m-d', '1987-11-11')
            ->willReturn('1987-11-11');

        $result = $this->model->getWithPrefilledData($this->quote);
        static::assertEquals('1987-11-11', $result['date_of_birth']);
    }

    public function testGetBasicDataNoAccessTokenForGuests(): void
    {
        $expected = 'company';
        $this->dependencyMocks['typeResolver']->method('getData')
            ->willReturn($expected);
        $this->quote->method('getCustomerId')
            ->willReturn(null);

        $result = $this->model->getBasicData($this->quote);
        static::assertArrayNotHasKey('klarna_access_token', $result);
    }

    public function testGetBasicDataLoggedInCustomerButNoAccessTokenSaved(): void
    {
        $this->quote->method('getCustomerId')
            ->willReturn('1');
        $this->dependencyMocks['service']->expects(static::once())
            ->method('getAccessToken')
            ->willReturn($this->container);
        $this->container->method('hasAccessToken')
            ->willReturn(false);

        $result = $this->model->getBasicData($this->quote);
        static::assertArrayNotHasKey('klarna_access_token', $result);
    }

    public function testGetBasicDataLoggedInCustomerAndAccessTokenSaved(): void
    {
        $this->quote->method('getCustomerId')
            ->willReturn('1');
        $this->dependencyMocks['service']->expects(static::once())
            ->method('getAccessToken')
            ->willReturn($this->container);
        $this->container->method('hasAccessToken')
            ->willReturn(true);
        $this->container->method('getAccessToken')
            ->willReturn('123');

        $result = $this->model->getBasicData($this->quote);
        static::assertArrayHasKey('klarna_access_token', $result);
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(Generator::class);

        $store = $this->mockFactory->create(Store::class);
        $this->address = $this->mockFactory->create(Address::class);
        $this->quote = $this->mockFactory->create(Quote::class, [
            'getStore',
            'getBillingAddress',
            'getCustomerIsGuest'
        ], [
            'getCustomerDob',
            'getCustomerId',
        ]);
        $this->quote->method('getStore')
            ->willReturn($store);
        $this->quote->method('getBillingAddress')
            ->willReturn($this->address);
        $this->container = $this->mockFactory->create(Container::class);
    }
}
