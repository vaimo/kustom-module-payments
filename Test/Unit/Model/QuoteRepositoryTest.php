<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model;

use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\ResourceModel\Quote\Collection as QuoteCollection;
use Klarna\Kp\Model\ResourceModel\Quote as QuoteResource;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @coversDefaultClass \Klarna\Kp\Model\QuoteRepository
 */
class QuoteRepositoryTest extends TestCase
{
    /**
     * @var QuoteRepositoryInterface|\Klarna\Kp\Model\QuoteRepository
     */
    private $model;

    /**
     * @var QuoteCollection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionMock;

    /**
     * @var Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteMock;

    /**
     * @var CartInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mageQuoteMock;

    /**
     * @var DataObject\|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectMock;

    /**
     * @covers ::getById()
     */
    public function testGetByIdWithException()
    {
        $this->expectException(NoSuchEntityException::class);

        $cartId = 14;

        $this->dependencyMocks['modelFactory']->expects($this->once())
            ->method('create')
            ->willReturn($this->quoteMock);
        $this->quoteMock->expects(static::once())
            ->method('getId')
            ->willReturn(null);

        $this->model->getById($cartId);
    }

    /**
     *
     * @covers ::getById()
     * @covers ::cacheInstance()
     * @covers Quote::getSessionId
     * @covers Quote::getClientToken
     */
    public function testGetById()
    {
        $cartId = 15;

        $this->dependencyMocks['modelFactory']->expects($this->once())
            ->method('create')
            ->willReturn($this->quoteMock);
        $this->quoteMock->expects(static::once())
            ->method('getId')
            ->willReturn($cartId);

        static::assertEquals($this->quoteMock, $this->model->getById($cartId));
    }

    /**
     * @covers ::getByQuoteId()
     */
    public function testGetByQuoteIdWithException()
    {
        $this->expectException(NoSuchEntityException::class);

        $cartId = 14;

        $this->dependencyMocks['modelFactory']->expects($this->once())
            ->method('create')
            ->willReturn($this->quoteMock);
        $this->quoteMock->expects(static::once())
            ->method('getId')
            ->willReturn(null);

        $this->model->getByQuoteId($cartId);
    }

    /**
     *
     * @covers ::getByQuoteId()
     * @covers ::cacheInstance()
     * @covers Quote::getSessionId
     * @covers Quote::getClientToken
     */
    public function testGetByQuoteId()
    {
        $quoteId = 15;

        $this->dependencyMocks['modelFactory']->expects($this->once())
            ->method('create')
            ->willReturn($this->quoteMock);
        $this->quoteMock->expects(static::once())
            ->method('getId')
            ->willReturn($quoteId);

        static::assertEquals($this->quoteMock, $this->model->getByQuoteId($quoteId));
    }

    /**
     * @covers ::getActiveByQuote()
     */
    public function testGetActiveByQuoteWithException()
    {
        $this->mageQuoteMock->expects(static::once())
            ->method('getId')
            ->willReturn(14);

        $this->setupQuoteCollectionMocks();
        $this->collectionMock->expects(static::once())
            ->method('getData')
            ->with('payments_quote_id')
            ->willReturn(null);

        $this->expectException(NoSuchEntityException::class);

        $this->model->getActiveByQuote($this->mageQuoteMock);
    }

    /**
     * @covers ::getActiveByQuote()
     */
    public function testGetActiveByQuote()
    {
        $klarnaQuoteId = '14';

        $this->setupQuoteCollectionMocks();
        $this->collectionMock->expects(static::once())
            ->method('getData')
            ->willReturn((int) $klarnaQuoteId);

        $this->dependencyMocks['modelFactory']->expects($this->once())
            ->method('create')
            ->willReturn($this->quoteMock);
        $this->quoteMock->method('getId')
            ->willReturn($klarnaQuoteId);

        $klarnaQuote = $this->model->getActiveByQuote($this->mageQuoteMock);

        static::assertEquals($klarnaQuote->getId(), $klarnaQuoteId);
    }

    /**
     * @covers ::getActiveByQuoteId()
     */
    public function testGetActiveByQuoteId()
    {
        $klarnaQuoteId = '14';

        $this->setupQuoteCollectionMocks();
        $this->collectionMock->expects(static::once())
            ->method('getData')
            ->willReturn((int) $klarnaQuoteId);

        $this->dependencyMocks['modelFactory']->expects($this->once())
            ->method('create')
            ->willReturn($this->quoteMock);
        $this->quoteMock->method('getId')
            ->willReturn($klarnaQuoteId);

        $klarnaQuote = $this->model->getActiveByQuoteId($klarnaQuoteId);

        static::assertEquals($klarnaQuote->getId(), $klarnaQuoteId);
    }

    /**
     * @covers ::save()
     */
    public function testSave()
    {
        $this->dependencyMocks['resourceModel']->expects($this->once())
            ->method('save')
            ->willReturn($this->quoteMock);

        $this->model->save($this->quoteMock);
    }

    /**
     * @covers ::save()
     */
    public function testSaveWithException()
    {
        $this->expectException(CouldNotSaveException::class);

        $exceptionMessage = 'No such entity with payments_quote_id = ';
        $this->dependencyMocks['resourceModel']->expects(static::once())
            ->method('save')
            ->with($this->quoteMock)
            ->willThrowException(new \Exception($exceptionMessage));

        $this->model->save($this->quoteMock);
    }

    /**
     * @covers ::delete()
     * @covers Quote::getSessionId
     * @covers Quote::getClientToken
     */
    public function testDelete()
    {
        $this->dependencyMocks['resourceModel']->expects($this->once())
            ->method('delete');

        $this->model->delete($this->quoteMock);
    }

    /**
     * @covers ::deleteById()
     * @covers ::delete()
     * @covers ::getById()
     * @covers ::cacheInstance()
     * @covers Quote::getSessionId
     * @covers Quote::getClientToken
     */
    public function testDeleteById()
    {
        $quoteId = 14;

        $this->dependencyMocks['modelFactory']->expects($this->once())
            ->method('create')
            ->willReturn($this->quoteMock);
        $this->quoteMock->expects($this->exactly(1))
            ->method('getId')
            ->willReturn($quoteId);
        $this->dependencyMocks['resourceModel']->expects($this->once())
            ->method('delete');

        $this->model->deleteById($quoteId);
    }

    /**
     * @covers ::getPaymentsQuotes()
     */
    public function testGetPaymentsQuotes()
    {
        $pageSize = 10;
        $order = 'desc';
        $expected = [
            ['payments_quote_id' => 1, 'session_id' => 'id', 'client_token' => 'token'],
            ['payments_quote_id' => 2, 'session_id' => 'id', 'client_token' => 'token'],
            ['payments_quote_id' => 3, 'session_id' => 'id', 'client_token' => 'token']
        ];

        $this->dependencyMocks['quoteCollectionFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->collectionMock);
        $this->collectionMock->expects(static::once())
            ->method('setPageSize')
            ->with($pageSize)
            ->willReturn($this->collectionMock);
        $this->collectionMock->expects(static::once())
            ->method('setOrder')
            ->with('payments_quote_id', strtoupper($order))
            ->willReturn($this->collectionMock);
        $this->collectionMock->expects(static::once())
            ->method('getItems')
            ->willReturn([
                $this->dataObjectMock,
                $this->dataObjectMock,
                $this->dataObjectMock
            ]);
        $this->dataObjectMock->expects(static::exactly(3))
            ->method('getData')
            ->willReturnOnConsecutiveCalls(
                $expected[0],
                $expected[1],
                $expected[2]
            );

        static::assertSame($expected, $this->model->getPaymentsQuotes($pageSize, $order));
    }

    /**
     * @covers ::getPaymentsQuotes()
     */
    public function testGetPaymentsQuotesEmptyResult()
    {
        $pageSize = 5;
        $order = 'desc';
        $expected = [];

        $this->dependencyMocks['quoteCollectionFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->collectionMock);
        $this->collectionMock->expects(static::once())
            ->method('setPageSize')
            ->with($pageSize)
            ->willReturn($this->collectionMock);
        $this->collectionMock->expects(static::once())
            ->method('setOrder')
            ->with('payments_quote_id', strtoupper($order))
            ->willReturn($this->collectionMock);
        $this->collectionMock->expects(static::once())
            ->method('getItems')
            ->willReturn([]);

        static::assertSame($expected, $this->model->getPaymentsQuotes($pageSize, $order));
    }

    /**
     * @covers ::getPaymentsQuotes()
     */
    public function testGetPaymentsQuotesInvalidOrderParameter()
    {
        $pageSize = 10;
        $order = 'unknown';

        static::expectException(\InvalidArgumentException::class);
        static::expectExceptionMessage('$order must be either DESC or ASC');

        $this->model->getPaymentsQuotes($pageSize, $order);
    }

    private function setupQuoteCollectionMocks()
    {
        $this->dependencyMocks['quoteCollectionFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->collectionMock);
        $this->collectionMock->expects(static::exactly(2))
            ->method('addFieldToFilter')
            ->willReturn($this->collectionMock);
        $this->collectionMock->expects(static::once())
            ->method('setOrder')
            ->willReturn($this->collectionMock);
        $this->collectionMock->expects(static::once())
            ->method('setPageSize')
            ->with(1)
            ->willReturn($this->collectionMock);
        $this->collectionMock->expects(static::once())
            ->method('getFirstItem')
            ->willReturn($this->collectionMock);
    }

    protected function setUp(): void
    {

        $this->model = $this->setUpMocks(QuoteRepository::class, [
            QuoteFactory::class => ['create'],
            QuoteResource::class => ['save', 'load', 'delete'],
            QuoteCollectionFactory::class => ['create'],
        ]);

        $this->mageQuoteMock = $this->createSingleMock(\Magento\Quote\Model\Quote::class);
        $this->quoteMock = $this->createSingleMock(Quote::class);
        $this->dataObjectMock = $this->createSingleMock(DataObject::class);
        $this->collectionMock = $this->createSingleMock(QuoteCollection::class,
            [
                'addFieldToFilter',
                'setOrder',
                'getFirstItem',
                'getData',
                'getItems',
                'setPageSize'
            ],
            [
                'create'
            ]
        );
    }
}
