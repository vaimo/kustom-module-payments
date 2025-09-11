<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Cron;

use Klarna\Kp\Model\ResourceModel\Quote\Collection as KlarnaQuoteCollection;
use Klarna\Kp\Model\ResourceModel\Quote\CollectionFactory as KlarnaQuoteCollectionFactory;

/**
 * @internal
 */
class CleanKlarnaQuoteTableCronJob
{
    public const LIFETIME_SECONDS = 604800;

    public const PAGE_SIZE = 50;

    /**
     * @var KlarnaQuoteCollectionFactory $collectionFactory
     */
    private KlarnaQuoteCollectionFactory $collectionFactory;

    /**
     * @param KlarnaQuoteCollectionFactory $collectionFactory
     * @codeCoverageIgnore
     */
    public function __construct(KlarnaQuoteCollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Clean old entries in the database table klarna_payments_quote
     *
     * @return void
     */
    public function execute(): void
    {
        $collection = $this->getCollection();
        $collection->setPageSize($this->getPageSize());

        $lastPage = $collection->getSize() ? $collection->getLastPageNumber() : 0;
        for ($currentPage = $lastPage; $currentPage >= 1; $currentPage--) {
            $collection->setCurPage($currentPage);
            $collection->walk('delete');
            $collection->clear();
        }
    }

    /**
     * Getting back the collection of klarna_payments_quote entries
     *
     * @return KlarnaQuoteCollection
     */
    private function getCollection(): KlarnaQuoteCollection
    {
        $collection = $this->collectionFactory->create();
        $lifeTime = $this->getLifeTime();

        $collection->addFieldToFilter('updated_at', ['to' => date("Y-m-d", time() - $lifeTime)]);
        $collection->addFieldToSelect('payments_quote_id');

        return $collection;
    }

    /**
     * Its a public method so that it can be extended by other modules.
     *
     * @return int
     */
    public function getPageSize(): int
    {
        return self::PAGE_SIZE;
    }

    /**
     * Its a public method so that it can be extended by other modules.
     *
     * @return int
     */
    public function getLifeTime(): int
    {
        return self::LIFETIME_SECONDS;
    }
}
