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
use Klarna\Kp\Model\Quote;
use Magento\Framework\Math\Random;
use Magento\Framework\App\Request\Http;
use Magento\Quote\Model\QuoteIdToMaskedQuoteIdInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * @internal
 */
class CurrentSessionDataTest extends ControllerTestCase
{
    /**
     * @var ResourceConnection
     */
    private $connection;
    /**
     * @var Quote
     */
    private $klarnaQuote;
    /**
     * @var Random
     */
    private $random;

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testOnSuccessfulResponseWeShouldReceiveAFullCreateOrderDataForTheCustomer(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        $quoteId = (int)$magentoQuote->getId();

        $this->saveQuoteIdMask($quoteId, $this->random->getRandomString(32));
        $quoteIdMask = $this->getMaskedQuoteId($quoteId);

        $this->klarnaQuote->setQuoteId($quoteId);
        $this->klarnaQuote->setIsActive(1);
        $this->klarnaQuote->save();

        $response = $this->sendRequest(
            [],
            sprintf('checkout/klarna/currentSessionData/?maskedQuoteId=%s', $quoteIdMask),
            Http::METHOD_GET
        );

        $this->assertEquals(200, $response['statusCode']);
        $expectedKeys = ['billing_address', 'shipping_address', 'order_lines', 'purchase_country', 'purchase_currency'];
        $this->assertEquals($expectedKeys, array_intersect($expectedKeys, array_keys($response['body']['data'])));
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testReturnsErrorWithAMessageOnFailureIfWrongQuoteIdMaskPassed(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();
        $quoteId = (int)$magentoQuote->getId();

        $this->saveQuoteIdMask($quoteId, $this->random->getRandomString(32));
        $quoteIdMask = $this->getMaskedQuoteId($quoteId);

        $response = $this->sendRequest(
            [],
            sprintf('checkout/klarna/currentSessionData/?maskedQuoteId=%s', $quoteIdMask . '+wrong'),
            Http::METHOD_GET
        );

        static::assertEquals(400, $response['statusCode']);
        static::assertEquals("No such entity with cartId = ", $response['body']['error']);
        static::assertEquals('Unable to retrieve Klarna payments session.', $response['body']['message']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testReturnsErrorWithAMessageOnFailureIfKlarnaQuoteIsNotAvailable(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();
        $quoteId = (int)$magentoQuote->getId();

        $this->saveQuoteIdMask($quoteId, $this->random->getRandomString(32));
        $quoteIdMask = $this->getMaskedQuoteId($quoteId);

        $response = $this->sendRequest(
            [],
            sprintf('checkout/klarna/currentSessionData/?maskedQuoteId=%s', $quoteIdMask),
            Http::METHOD_GET
        );

        static::assertEquals(400, $response['statusCode']);
        static::assertEquals("No such entity with quote_id = {$quoteId}", $response['body']['error']);
        static::assertEquals('Unable to retrieve Klarna payments session.', $response['body']['message']);
    }

    /**
     * @param int $quoteId
     * @param string $quoteIdMask
     * @return void
     */
    private function saveQuoteIdMask(int $quoteId, string $quoteIdMask): void
    {
        $quoteIdMaskTableName = $this->connection->getTableName('quote_id_mask');
        $connection = $this->connection->getConnection();
        $quoteIdMaskInsertQuery = sprintf(
            'Insert into %s (quote_id, masked_id) values (%d, "%s")',
            $quoteIdMaskTableName,
            $quoteId,
            $quoteIdMask
        );
        $connection->query($quoteIdMaskInsertQuery);
    }

    /**
     * @param int $quoteId
     * @return mixed
     */
    public function getMaskedQuoteId(int $quoteId)
    {
        $maskedQuoteIdObject = $this->objectManager->get(QuoteIdToMaskedQuoteIdInterface::class);
        return $maskedQuoteIdObject->execute($quoteId);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->klarnaQuote = $this->objectManager->get(Quote::class);
        $this->connection = $this->objectManager->get(ResourceConnection::class);
        $this->random = $this->objectManager->get(Random::class);
    }
}
