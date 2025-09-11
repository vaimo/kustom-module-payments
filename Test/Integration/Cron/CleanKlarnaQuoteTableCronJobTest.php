<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Integration\Cron;

use Klarna\Base\Test\Integration\Helper\GenericTestCase;
use Klarna\Kp\Cron\CleanKlarnaQuoteTableCronJob;
use Klarna\Kp\Model\QuoteRepository;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;

/**
 * @internal
 */
class CleanKlarnaQuoteTableCronJobTest extends GenericTestCase
{
    /**
     * @var ResourceConnection
     */
    private $connection;
    /**
     * @var CleanKlarnaQuoteTableCronJob
     */
    private $cleanKlarnaQuoteTableCronJob;
    /**
     * @var string
     */
    private $quoteId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = $this->objectManager->get(ResourceConnection::class);
        $this->cleanKlarnaQuoteTableCronJob = $this->objectManager->get(CleanKlarnaQuoteTableCronJob::class);

        $quote = $this->session->getQuote();
        $quote->save();
        $this->quoteId = $quote->getId();
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteNoEntriesInTableImpliesNoEntriesAreInTheTableAfterTheExecution(): void
    {
        $connection = $this->connection->getConnection();

        $query = "SELECT * FROM klarna_payments_quote";
        $oldResult = $connection->fetchAll($query);
        static::assertEquals(0, count($oldResult));

        $this->cleanKlarnaQuoteTableCronJob->execute();

        $newResult = $connection->fetchAll($query);
        static::assertEquals(0, count($newResult));
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteOneDeprecatedEntryInTableImpliesEntryIsRemoved(): void
    {
        $connection = $this->connection->getConnection();

        $query = "INSERT INTO klarna_payments_quote(quote_id, updated_at) VALUES ('" . $this->quoteId . "', '2023-09-03 07:48:20')";
        $connection->query($query);

        $query = "SELECT * FROM klarna_payments_quote";
        $oldResult = $connection->fetchAll($query);
        static::assertEquals(1, count($oldResult));

        $this->cleanKlarnaQuoteTableCronJob->execute();

        $newResult = $connection->fetchAll($query);
        static::assertEquals(0, count($newResult));
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteOneRecentEntryInTableImpliesNoEntryIsRemoved(): void
    {
        $connection = $this->connection->getConnection();

        $query = "INSERT INTO klarna_payments_quote(quote_id) VALUES ('" . $this->quoteId . "')";
        $connection->query($query);

        $query = "SELECT * FROM klarna_payments_quote";
        $oldResult = $connection->fetchAll($query);
        static::assertEquals(1, count($oldResult));

        $this->cleanKlarnaQuoteTableCronJob->execute();

        $newResult = $connection->fetchAll($query);
        static::assertEquals(1, count($newResult));
    }
}
