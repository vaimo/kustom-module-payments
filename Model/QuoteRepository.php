<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model;

use Klarna\Base\Model\RepositoryAbstract;
use Klarna\Kp\Api\QuoteAuthorizationTokenRepositoryInterface;
use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\ResourceModel\Quote as QuoteResource;
use Klarna\Kp\Model\ResourceModel\Quote\Collection as QuoteCollection;
use Klarna\Kp\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface as MageQuoteInterface;

/**
 * @internal
 */
class QuoteRepository extends RepositoryAbstract implements
    QuoteRepositoryInterface,
    QuoteAuthorizationTokenRepositoryInterface
{
    /**
     * @var QuoteCollectionFactory
     */
    private QuoteCollectionFactory $quoteCollectionFactory;

    /**
     * @param QuoteFactory $modelFactory
     * @param QuoteResource $resourceModel
     * @param QuoteCollectionFactory $quoteCollectionFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        QuoteFactory $modelFactory,
        QuoteResource $resourceModel,
        QuoteCollectionFactory $quoteCollectionFactory
    ) {
        parent::__construct($resourceModel, $modelFactory);
        $this->quoteCollectionFactory = $quoteCollectionFactory;
    }

    /**
     * Get quote by Magento quote
     *
     * @param MageQuoteInterface $mageQuote
     * @return QuoteInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     *
     * @SuppressWarnings(PMD.StaticAccess)
     */
    public function getActiveByQuote(MageQuoteInterface $mageQuote): QuoteInterface
    {
        $paymentsQuoteId = $this->getActiveByQuoteIdModel((string) $mageQuote->getId());
        return $this->getByKeyValuePair('payments_quote_id', $paymentsQuoteId);
    }

    /**
     * @inheritDoc
     *
     * @throws NoSuchEntityException
     */
    public function getActiveByQuoteId(string $mageQuoteId): QuoteInterface
    {
        $quoteId = $this->getActiveByQuoteIdModel($mageQuoteId);
        return $this->getByKeyValuePair('payments_quote_id', $quoteId);
    }

    /**
     * Get active quote by Magento quote ID
     *
     * @param string $mageQuoteId
     * @return string
     * @throws NoSuchEntityException
     */
    private function getActiveByQuoteIdModel(string $mageQuoteId): string
    {
        $collection =  $this->createEmptyCollection();
        $quoteId = $collection->addFieldToFilter('quote_id', $mageQuoteId)
            ->addFieldToFilter('is_active', 1)
            ->setOrder('payments_quote_id', 'desc')
            ->setPageSize(1)
            ->getFirstItem()
            ->getData('payments_quote_id');

        if (!$quoteId) {
            throw NoSuchEntityException::singleField('quote_id', $mageQuoteId);
        }
        return (string) $quoteId;
    }

    /**
     * Getting back a empty collection of Klarna Quotes
     *
     * @return QuoteCollection
     */
    private function createEmptyCollection(): QuoteCollection
    {
        return $this->quoteCollectionFactory->create();
    }

    /**
     * Delete quote by ID
     *
     * @param int $id
     * @return void
     * @throws NoSuchEntityException
     */
    public function deleteById($id): void
    {
        $this->delete($this->getById($id));
    }

    /**
     * Deleting all entries by Magento quote ID
     *
     * @param string $magentoQuoteId
     * @throws \Exception
     */
    public function deleteByMagentoQuoteId(string $magentoQuoteId): void
    {
        $klarnaQuoteCollection = $this->createEmptyCollection();
        $klarnaQuoteCollection->addFieldToFilter('quote_id', $magentoQuoteId);

        /** @var Quote $item */
        foreach ($klarnaQuoteCollection->getItems() as $item) {
            $this->delete($item);
        }
    }

    /**
     * Get quote by ID
     *
     * @param int  $quoteId
     * @param bool $forceReload
     * @return QuoteInterface
     * @throws NoSuchEntityException
     *
     * @SuppressWarnings(PMD.BooleanArgumentFlag)
     */
    public function getById(int $quoteId, bool $forceReload = false): QuoteInterface
    {
        return $this->getByKeyValuePair('payments_quote_id', (string) $quoteId);
    }

    /**
     * Get quote by quote ID
     *
     * @param int $quoteId
     * @return QuoteInterface
     * @throws NoSuchEntityException
     */
    public function getByQuoteId(int $quoteId): QuoteInterface
    {
        return $this->getByKeyValuePair('quote_id', (string) $quoteId);
    }

    /**
     * Mark quote as inactive and cancel it with API
     *
     * @param QuoteInterface $quote
     * @throws CouldNotSaveException
     */
    public function markInactive(QuoteInterface $quote): void
    {
        $quote->setIsActive(0);
        $this->save($quote);
    }

    /**
     * Load quote by session_id
     *
     * @param string $sessionId
     * @param bool   $forceReload
     * @return QuoteInterface
     * @throws NoSuchEntityException
     *
     * @SuppressWarnings(PMD.BooleanArgumentFlag)
     */
    public function getBySessionId($sessionId, $forceReload = false): QuoteInterface
    {
        return $this->getByKeyValuePair('session_id', $sessionId);
    }

    /**
     * Getting back the authorization token
     *
     * @param string $authorizationToken
     * @return QuoteInterface
     * @throws NoSuchEntityException
     */
    public function getByAuthorizationToken(string $authorizationToken): QuoteInterface
    {
        return $this->getByKeyValuePair('authorization_token', $authorizationToken);
    }

    /**
     * Returns true if the session id is already saved in the database
     *
     * @param string $sessionId
     * @return bool
     */
    public function existSessionIdEntry(string $sessionId): bool
    {
        return $this->existEntryByKeyValuePair('session_id', $sessionId);
    }

    /**
     * Returns true if a row is found based on the Magento quote ID
     *
     * @param string $quoteId
     * @return bool
     */
    public function existsEntryByQuoteId(string $quoteId): bool
    {
        return $this->existEntryByKeyValuePair('quote_id', $quoteId);
    }

    /**
     * Get quotes with limit and order of quote_id (DESC | ASC)
     *
     * @param int $pageSize
     * @param string $order
     * @return array[]
     */
    public function getPaymentsQuotes(int $pageSize, string $order): array
    {
        $order = strtoupper($order);
        if ($order !== 'DESC' && $order !== 'ASC') {
            throw new \InvalidArgumentException('$order must be either DESC or ASC');
        }

        $data = $this->createEmptyCollection()
            ->setPageSize($pageSize)
            ->setOrder('payments_quote_id', $order)
            ->getItems();

        $result = [];
        foreach ($data as $item) {
            $result[] = $item->getData();
        }
        return $result;
    }
}
