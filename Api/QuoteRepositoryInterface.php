<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api;

use Magento\Quote\Api\Data\CartInterface as MageQuoteInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * @api
 */
interface QuoteRepositoryInterface
{
    /**
     * Saving the quote
     *
     * @param AbstractModel $page
     * @return AbstractModel
     */
    public function save(AbstractModel $page);

    /**
     * Getting the quote by id
     *
     * @param int $quoteId
     * @param bool $forceReload
     * @return QuoteInterface
     */
    public function getById(int $quoteId, bool $forceReload = false): QuoteInterface;

    /**
     * Getting a active quote
     *
     * @param MageQuoteInterface $mageQuote
     * @return QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getActiveByQuote(MageQuoteInterface $mageQuote): QuoteInterface;

    /**
     * Deleting a entry by object.
     *
     * @param AbstractModel $page
     * @return void
     */
    public function delete(AbstractModel $page);

    /**
     * Deleting a entry by id.
     *
     * @param int $id
     * @return void
     */
    public function deleteById($id);

    /**
     * Mark quote as inactive and cancel it with API
     *
     * @param QuoteInterface $quote
     */
    public function markInactive(QuoteInterface $quote);

    /**
     * Load quote by session_id
     *
     * @param string $sessionId
     * @param bool   $forceReload
     * @return QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBySessionId(string $sessionId, $forceReload = false);

    /**
     * Returns true if there is a entry in the database with the given session id
     *
     * @param string $sessionId
     * @return bool
     */
    public function existSessionIdEntry(string $sessionId): bool;

    /**
     * Get quote by Magento quote
     *
     * @param string $mageQuoteId
     * @return QuoteInterface
     */
    public function getActiveByQuoteId(string $mageQuoteId): QuoteInterface;
}
