<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\System\Message;

use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Kp\Model\System\Message\AuthorizationCallbackHealthCheck;

class AuthorizationCallbackHealthCheckTest extends TestCase
{
    /**
     * @var AuthorizationCallbackHealthCheck
     */
    private $authorizationCallbackHealthCheck;

    protected function setUp(): void
    {
        $this->authorizationCallbackHealthCheck = parent::setUpMocks(AuthorizationCallbackHealthCheck::class);
    }

    /**
     * @dataProvider orderAttemptsDataProvider
     *
     * @param $totalCreateOrderAttempts
     * @param $failedAttempts
     * @param $expectedResult
     * @return void
     * @throws \DateMalformedStringException
     */
    public function testIsMoreThanSpecifiedPercentStatus403InLastXDays(
        $totalCreateOrderAttempts,
        $failedAttempts,
        $expectedResult
    ): void {
        $this->dependencyMocks['logRepository']
            ->method('getTotalCreateOrdersAttempts')
            ->willReturn($totalCreateOrderAttempts);
        $this->dependencyMocks['logRepository']
            ->method('getTotalFailedOrdersAttempts')
            ->willReturn($failedAttempts);

        $this->assertEquals($expectedResult, $this->authorizationCallbackHealthCheck->isDisplayed());
    }

    /**
     * @return void
     */
    public function testSeverityShouldBe1Or2(): void
    {
        $this->assertContains($this->authorizationCallbackHealthCheck->getSeverity(), [1, 2]);
    }

    public function orderAttemptsDataProvider(): array
    {
        return [
            [
                'totalCreateOrderAttempts' => 100,
                'failedAttempts' => 30,
                'expectedResult' => false
            ],
            [
                'totalCreateOrderAttempts' => 100,
                'failedAttempts' => 29,
                'expectedResult' => false
            ],
            [
                'totalCreateOrderAttempts' => 100,
                'failedAttempts' => 10,
                'expectedResult' => false
            ],
            [
                // case: division by zero Exception
                'totalCreateOrderAttempts' => 0,
                'failedAttempts' => 10,
                'expectedResult' => false
            ],
            [
                // case: there is no failed attempts
                'totalCreateOrderAttempts' => 100,
                'failedAttempts' => 0,
                'expectedResult' => false
            ],
            [
                'totalCreateOrderAttempts' => 100,
                'failedAttempts' => 31,
                'expectedResult' => true
            ],
            [
                'totalCreateOrderAttempts' => 100,
                'failedAttempts' => 100,
                'expectedResult' => true
            ],
        ];
    }
}
