<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Integration\Controller\Klarna;

use Klarna\Base\Test\Integration\Helper\ControllerTestCase;
use Klarna\Kp\Api\AuthorizationCallbackStatusInterface;
use Klarna\Kp\Model\Quote;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResourceConnection;

/**
 * @internal
 */
class AuthorizeTest extends ControllerTestCase
{
    /**
     * @var Quote
     */
    private $klarnaQuote;
    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteDryRunParameterWithValueIsGiven(): void
    {
        $numberOfOrdersBefore = $this->getNumberMagentoOrders();

        $result = $this->sendRequest(
            ['session_id' => '1', 'authorization_token' => '1'],
            'checkout/klarna/authorize?dryRun=1',
            Http::METHOD_POST
        );

        $numberOfOrdersAfter = $this->getNumberMagentoOrders();

        static::assertEquals($numberOfOrdersBefore, $numberOfOrdersAfter);
        static::assertEquals(200, $result['statusCode']);
        static::assertEquals('The checkout/klarna/authorize?dryRun=1 is accessible.', $result['body']['message']);
        static::assertTrue(isset($result['body']['timestamp']));
        static::assertEquals(200, $result['body']['code']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    private function getNumberMagentoOrders(): int
    {
        $connection = $this->connection->getConnection();

        $query = $connection->select()
            ->from('sales_order', ['number_orders' => new \Zend_Db_Expr('COUNT(*)')]);

        $result = $connection->fetchAll($query);
        return (int) $result[0]['number_orders'];
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteMissingSessionIdParameter(): void
    {
        $numberOfOrdersBefore = $this->getNumberMagentoOrders();

        $result = $this->sendRequest(
            ['authorization_token' => '1'],
            'checkout/klarna/authorize?token=2',
            Http::METHOD_POST
        );

        $numberOfOrdersAfter = $this->getNumberMagentoOrders();

        static::assertEquals($numberOfOrdersBefore, $numberOfOrdersAfter);
        static::assertEquals(400, $result['statusCode']);
        static::assertEquals('session_id is required.', $result['body']['error']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteMissingAuthorizationTokenParameter(): void
    {
        $numberOfOrdersBefore = $this->getNumberMagentoOrders();

        $result = $this->sendRequest(
            ['session_id' => '1'],
            'checkout/klarna/authorize?token=2',
            Http::METHOD_POST
        );

        $numberOfOrdersAfter = $this->getNumberMagentoOrders();

        static::assertEquals($numberOfOrdersBefore, $numberOfOrdersAfter);
        static::assertEquals(400, $result['statusCode']);
        static::assertEquals('authorization_token is required.', $result['body']['error']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteMissingTokenParameter(): void
    {
        $numberOfOrdersBefore = $this->getNumberMagentoOrders();

        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        $this->klarnaQuote->setSessionId('1');
        $this->klarnaQuote->setQuoteId($magentoQuote->getId());
        $this->klarnaQuote->setAuthTokenCallbackToken('my_token');
        $this->klarnaQuote->save();

        $result = $this->sendRequest(
            ['session_id' => '1', 'authorization_token' => '1'],
            'checkout/klarna/authorize',
            Http::METHOD_POST
        );

        $numberOfOrdersAfter = $this->getNumberMagentoOrders();

        static::assertEquals($numberOfOrdersBefore, $numberOfOrdersAfter);
        static::assertEquals(400, $result['statusCode']);
        static::assertEquals('Invalid value of "" provided for the token field.', $result['body']['error']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteNoSessionIdEntryGivenInDatabase(): void
    {
        $numberOfOrdersBefore = $this->getNumberMagentoOrders();

        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        $this->klarnaQuote->setQuoteId($magentoQuote->getId());
        $this->klarnaQuote->setAuthTokenCallbackToken('my_token');
        $this->klarnaQuote->save();

        $result = $this->sendRequest(
            ['session_id' => '1', 'authorization_token' => '1'],
            'checkout/klarna/authorize',
            Http::METHOD_POST
        );

        $numberOfOrdersAfter = $this->getNumberMagentoOrders();

        static::assertEquals($numberOfOrdersBefore, $numberOfOrdersAfter);
        static::assertEquals(400, $result['statusCode']);
        static::assertEquals('No such entity with session_id = 1', $result['body']['error']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteValidSessionIdAndTokenButAuthCallbackWorkflowStillRunning(): void
    {
        $numberOfOrdersBefore = $this->getNumberMagentoOrders();

        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        $this->klarnaQuote->setQuoteId($magentoQuote->getId());
        $this->klarnaQuote->setSessionId('1');
        $this->klarnaQuote->setAuthTokenCallbackToken('my_token');
        $this->klarnaQuote->setAuthCallbackActiveCurrentStatus(AuthorizationCallbackStatusInterface::IN_PROGRESS);
        $this->klarnaQuote->save();

        $result = $this->sendRequest(
            ['session_id' => '1', 'authorization_token' => '1'],
            'checkout/klarna/authorize?token=my_token',
            Http::METHOD_POST
        );

        $numberOfOrdersAfter = $this->getNumberMagentoOrders();

        static::assertEquals($numberOfOrdersBefore, $numberOfOrdersAfter);
        static::assertEquals(400, $result['statusCode']);
        static::assertEquals('Another authorization callback workflow is still in progress.', $result['body']['error']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecutePlacingOrderFailed(): void
    {
        $numberOfOrdersBefore = $this->getNumberMagentoOrders();

        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        $this->klarnaQuote->setQuoteId($magentoQuote->getId());
        $this->klarnaQuote->setSessionId('1');
        $this->klarnaQuote->setAuthTokenCallbackToken('my_token');
        $this->klarnaQuote->save();

        $result = $this->sendRequest(
            ['session_id' => '1', 'authorization_token' => '1'],
            'checkout/klarna/authorize?token=my_token',
            Http::METHOD_POST
        );

        $numberOfOrdersAfter = $this->getNumberMagentoOrders();

        static::assertEquals($numberOfOrdersBefore, $numberOfOrdersAfter);
        static::assertEquals(400, $result['statusCode']);
        static::assertEquals('The payment method you requested is not available.', $result['body']['error']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->klarnaQuote = $this->objectManager->get(Quote::class);
        $this->connection = $this->objectManager->get(ResourceConnection::class);
    }
}
