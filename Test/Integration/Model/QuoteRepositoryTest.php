<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Integration\Model;

use Klarna\Kp\Model\QuoteRepository;
use Klarna\Kp\Model\ResourceModel\Quote as KpQuote;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Checkout\Model\Session as MagentoSession;
use Magento\Framework\App\ResourceConnection;
use Magento\Quote\Model\Quote;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @internal
 */
class QuoteRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MagentoSession
     */
    private $session;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var QuoteRepository
     */
    private $klarnaQuoteRepository;
    /**
     * @var mixed KpQuote
     */
    private $kpQuote;
    /**
     * @var ResourceConnection
     */
    private $connection;
    /**
     * @var Quote
     */
    private $quote;

    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->session = $this->objectManager->get(MagentoSession::class);
        $this->kpQuote = $this->objectManager->get(KpQuote::class);
        $this->klarnaQuoteRepository = $this->objectManager->get(QuoteRepository::class);
        $this->connection = $this->objectManager->get(ResourceConnection::class);

        $this->quote = $this->session->getQuote();
        $this->quote->save();
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteNoEntryFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getActiveByQuote($this->quote);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteJustOneEntryExistsAndItsMarkedAsActive(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $insertData = [
            'session_id' => $expected,
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData);

        $result = $this->klarnaQuoteRepository->getActiveByQuote($this->quote);
        static::assertEquals($expected, $result->getSessionId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteJustOneEntryExistsButItsMarkedAsInactive(): void
    {
        $connection = $this->connection->getConnection();
        $insertData = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 0,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData);

        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getActiveByQuote($this->quote);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteSeveralEntriesExistsAndAllMarkedAsActive(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $insertData1 = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData1);

        $insertData2 = [
            'session_id' => '_a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData2);

        $insertData3 = [
            'session_id' => $expected,
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData3);

        $result = $this->klarnaQuoteRepository->getActiveByQuote($this->quote);
        static::assertEquals($expected, $result->getSessionId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteSeveralEntriesExistsButLastEntryIsMarkedAsInactive(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $insertData1 = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData1);

        $insertData2 = [
            'session_id' => $expected,
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData2);

        $insertData3 = [
            'session_id' => '_a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 0,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData3);

        $result = $this->klarnaQuoteRepository->getActiveByQuote($this->quote);
        static::assertEquals($expected, $result->getSessionId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteIdNoEntryFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteIdJustOneEntryExistsAndItsMarkedAsActive(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $insertData = [
            'session_id' => $expected,
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData);

        $result = $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());
        static::assertEquals($expected, $result->getSessionId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteIdJustOneEntryExistsButItsMarkedAsInactive(): void
    {
        $connection = $this->connection->getConnection();
        $insertData = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 0,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData);

        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteIdSeveralEntriesExistsAndAllMarkedAsActive(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $insertData1 = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData1);

        $insertData2 = [
            'session_id' => '_a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData2);

        $insertData3 = [
            'session_id' => $expected,
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData3);

        $result = $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());
        static::assertEquals($expected, $result->getSessionId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteIdSeveralEntriesExistsButLastEntryIsMarkedAsInactive(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $insertData1 = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData1);

        $insertData2 = [
            'session_id' => $expected,
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData2);

        $insertData3 = [
            'session_id' => '_a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 0,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData3);

        $result = $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());
        static::assertEquals($expected, $result->getSessionId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testDeleteByIdNoEntryFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testDeleteByIdEntryFoundAndItsMarkedAsInactive(): void
    {
        $connection = $this->connection->getConnection();
        $insertData = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 0,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData);

        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testDeleteByIdEntryFoundAndItsMarkedAsActive(): void
    {
        $connection = $this->connection->getConnection();
        $insertData = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];

        $connection->insert('klarna_payments_quote', $insertData);
        $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());

        $query = $connection->select()
            ->from('klarna_payments_quote')
            ->where('quote_id = ?', $this->quote->getId());
        $result = $connection->fetchAll($query);
        static::assertEquals($this->quote->getId(), $result[0]['quote_id']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testDeleteEntryFoundAndItsMarkedAsActive(): void
    {
        $connection = $this->connection->getConnection();
        $insertData = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];

        $connection->insert('klarna_payments_quote', $insertData);

        $klarnaQuote = $this->klarnaQuoteRepository->getActiveByQuote($this->quote);
        $this->klarnaQuoteRepository->delete($klarnaQuote);

        $query = $connection->select()
            ->from('klarna_payments_quote')
            ->where('quote_id = ?', $this->quote->getId());
        $result = $connection->fetchAll($query);
        static::assertEmpty($result);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetByIdNoEntryFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getById(999999999);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetByIdEntryFound(): void
    {
        $connection = $this->connection->getConnection();
        $insertData = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData);

        $query = $connection->select()
            ->from('klarna_payments_quote')
            ->where('quote_id = ?', $this->quote->getId());
        $result = $connection->fetchAll($query);
        $klarnaQuote = $this->klarnaQuoteRepository->getById((int) $result[0]['payments_quote_id']);

        static::assertEquals($this->quote->getId(), $klarnaQuote->getQuoteId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testMarkInactiveEntryStatusIsChangedToInactive(): void
    {
        $connection = $this->connection->getConnection();
        $insertData = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData);

        $query = $connection->select()
            ->from('klarna_payments_quote')
            ->where('quote_id = ?', $this->quote->getId());
        $result = $connection->fetchAll($query);
        $klarnaQuote = $this->klarnaQuoteRepository->getById((int) $result[0]['payments_quote_id']);

        $this->klarnaQuoteRepository->markInactive($klarnaQuote);

        $query = $connection->select()
            ->from('klarna_payments_quote')
            ->where('quote_id = ?', $this->quote->getId());
        $result = $connection->fetchAll($query);
        static::assertEquals(0, $result[0]['is_active']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testMarkInactiveEntryStatusWasAlreadyInactive(): void
    {
        $connection = $this->connection->getConnection();
        $insertData = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 0,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData);

        $query = $connection->select()
            ->from('klarna_payments_quote')
            ->where('quote_id = ?', $this->quote->getId());
        $result = $connection->fetchAll($query);
        $klarnaQuote = $this->klarnaQuoteRepository->getById((int) $result[0]['payments_quote_id']);

        $this->klarnaQuoteRepository->markInactive($klarnaQuote);

        $query = $connection->select()
            ->from('klarna_payments_quote')
            ->where('quote_id = ?', $this->quote->getId());
        $result = $connection->fetchAll($query);
        static::assertEquals(0, $result[0]['is_active']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSaveEntryIsSaved(): void
    {
        $expected = 'my_new_value';
        $connection = $this->connection->getConnection();
        $insertData = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 0,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData);

        $query = $connection->select()
            ->from('klarna_payments_quote')
            ->where('quote_id = ?', $this->quote->getId());
        $result = $connection->fetchAll($query);
        $klarnaQuote = $this->klarnaQuoteRepository->getById((int) $result[0]['payments_quote_id']);
        $klarnaQuote->setSessionId($expected);

        $this->klarnaQuoteRepository->save($klarnaQuote);

        $query = $connection->select()
            ->from('klarna_payments_quote')
            ->where('quote_id = ?', $this->quote->getId());
        $result = $connection->fetchAll($query);
        static::assertEquals($expected, $result[0]['session_id']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetBySessionIdNoEntryFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getBySessionId('999999999');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetBySessionIdEntryFound(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $insertData = [
            'session_id' => $expected,
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData);

        $result = $this->klarnaQuoteRepository->getBySessionId($expected);
        static::assertEquals($expected, $result->getSessionId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetByAuthorizationTokenNoEntryFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getByAuthorizationToken('999999999');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetByAuthorizationTokenEntryFound(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $insertData = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => $expected,
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData);

        $result = $this->klarnaQuoteRepository->getByAuthorizationToken($expected);
        static::assertEquals($expected, $result->getAuthorizationToken());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testDeleteByMagentoQuoteIdNoQuoteFoundImpliesNothingIsDeleted(): void
    {
        $connection = $this->connection->getConnection();

        $query = $connection->select()
            ->from('klarna_payments_quote')
            ->where('quote_id = ?', $this->quote->getId());
        $result = $connection->fetchAll($query);
        static::assertEquals(0, count($result));

        $this->klarnaQuoteRepository->deleteByMagentoQuoteId($this->quote->getId());

        $result = $connection->fetchAll($query);
        static::assertEquals(0, count($result));
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testDeleteByMagentoQuoteIdOneQuoteFoundImpliesOneEntryIsDeleted(): void
    {
        $connection = $this->connection->getConnection();
        $insertData = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData);

        $query = $connection->select()
            ->from('klarna_payments_quote')
            ->where('quote_id = ?', $this->quote->getId());
        $result = $connection->fetchAll($query);
        static::assertEquals(1, count($result));

        $this->klarnaQuoteRepository->deleteByMagentoQuoteId($this->quote->getId());

        $result = $connection->fetchAll($query);
        static::assertEquals(0, count($result));
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testDeleteByMagentoQuoteIdTwoQuotesFoundImpliesTwoEntriesIsDeleted(): void
    {
        $connection = $this->connection->getConnection();
        $insertDataOne = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $insertDataTwo = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertDataOne);
        $connection->insert('klarna_payments_quote', $insertDataTwo);

        $query = $connection->select()
            ->from('klarna_payments_quote')
            ->where('quote_id = ?', $this->quote->getId());
        $result = $connection->fetchAll($query);
        static::assertEquals(2, count($result));

        $this->klarnaQuoteRepository->deleteByMagentoQuoteId($this->quote->getId());

        $result = $connection->fetchAll($query);
        static::assertEquals(0, count($result));
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetPaymentsQuotesEntriesFound(): void
    {
            $insertData = [
                'payments_quote_id' => 2,
                'session_id' => 'c',
                'client_token' => 'd',
                'authorization_token' => 'token',
                'is_active' => 0,
                'quote_id' => $this->quote->getId(),
                'payment_methods' => 'klarna_payments',
                'payment_method_info' => 'klarna_payments',
                'customer_type' => 'guest',
                'order_id' => 1,
                'redirect_url' => 'http://redirect.url',
                'auth_callback_token' => 'auth_callback_token',
                'is_auth_callback_active' => 1,
                'is_kec_session' => 1,
                'request_data' => 'request_data',
                'created_at' => '2024-12-09 00:00:00',
                'updated_at' => '2024-12-09 00:00:00',
            ];

        $connection = $this->connection->getConnection();
        $connection->insert('klarna_payments_quote', $insertData);

        $result = $this->klarnaQuoteRepository->getPaymentsQuotes(1, 'desc');

        // Convert int values to string since that is what the repository returns
        $insertData['payments_quote_id'] = strval($insertData['payments_quote_id']);
        $insertData['is_active'] = strval($insertData['is_active']);
        $insertData['quote_id'] = strval($insertData['quote_id']);
        $insertData['order_id'] = strval($insertData['order_id']);
        $insertData['is_auth_callback_active'] = strval($insertData['is_auth_callback_active']);
        $insertData['is_kec_session'] = strval($insertData['is_kec_session']);

        static::assertSame($insertData, $result[0]);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetPaymentsQuotesEntriesNotFound(): void
    {
        $result = $this->klarnaQuoteRepository->getPaymentsQuotes(1, 'asc');
        static::assertSame([], $result);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetPaymentsQuotesEntriesInvalidOrderParameter(): void
    {
        static::expectException(\InvalidArgumentException::class);
        static::expectExceptionMessage('$order must be either DESC or ASC');

        $this->klarnaQuoteRepository->getPaymentsQuotes(1, 'invalid');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetByQuoteIdNoEntryFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getByQuoteId(999999999);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetByQuoteIdEntryFound(): void
    {
        $connection = $this->connection->getConnection();
        $insertData = [
            'session_id' => 'a',
            'client_token' => 'b',
            'authorization_token' => 'c',
            'is_active' => 1,
            'quote_id' => $this->quote->getId()
        ];
        $connection->insert('klarna_payments_quote', $insertData);

        $query = $connection->select()
            ->from('klarna_payments_quote')
            ->where('quote_id = ?', $this->quote->getId());
        $result = $connection->fetchAll($query);
        $klarnaQuote = $this->klarnaQuoteRepository->getByQuoteId((int) $result[0]['quote_id']);

        static::assertEquals($this->quote->getId(), $klarnaQuote->getQuoteId());
    }
}
