<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Unit\Observer;

use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Base\Helper\Debug\DebugDataObject;
use Klarna\Kp\Observer\DebugDataCollectorObserver;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;

/**
 * @internal
 */
class DebugDataCollectorObserverTest extends TestCase
{
    /**
     * @var DebugDataCollectorObserver
     */
    private $debugDataCollectorObserver;

    /**
     * @var Observer|MockObject
     */
    private $observer;

    /**
     * @var Event|MockObject
     */
    private $event;

    /**
     * @var DebugDataObject|MockObject
     */
    private $debugDataObject;

    protected function setUp(): void
    {
        $this->debugDataCollectorObserver = parent::setUpMocks(DebugDataCollectorObserver::class);
        $this->debugDataObject = $this->createSingleMock(DebugDataObject::class);
        $this->observer = $this->createSingleMock(Observer::class);
        $this->event = $this->createSingleMock(Event::class, [], ['getDebugDataObject']);
    }

    /**
     * @dataProvider executionDataProvider
     */
    public function testExecutionAddsStringifiedTableDataToDebugDataObject($data, $expected): void
    {
        $this->debugDataObject->expects(static::once())
            ->method('addData');
        $this->event->expects(static::once())
            ->method('getDebugDataObject')
            ->willReturn($this->debugDataObject);
        $this->observer->expects(static::once())
            ->method('getEvent')
            ->willReturn($this->event);

        $this->dependencyMocks['quoteRepository']->expects(static::once())
            ->method('getPaymentsQuotes')
            ->with(1000, 'DESC')
            ->willReturn($data);

        $this->dependencyMocks['stringifyDbTableData']->expects(static::once())
            ->method('getStringData')
            ->with($data)
            ->willReturn($expected);

        $this->debugDataCollectorObserver->execute($this->observer);
    }


    public function executionDataProvider(): array
    {
        return [
            'empty data' => [
                [],
                '[]'
            ],
            'non-empty data' => [
                [
                    ['payments_quote_id' => 1],
                    ['payments_quote_id' => 2],
                ],
                '[{"payments_quote_id":1},{"payments_quote_id":2}]'
            ]
        ];
    }
}
