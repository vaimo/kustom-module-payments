<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Observer;

use Klarna\Base\Helper\Debug\DebugDataObject;
use Klarna\Base\Helper\Debug\StringifyDbTableData;
use Klarna\Kp\Model\QuoteRepository;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @internal
 */
class DebugDataCollectorObserver implements ObserverInterface
{
    /**
     * @var StringifyDbTableData
     */
    private StringifyDbTableData $stringifyDbTableData;

    /**
     * @var QuoteRepository
     */
    private QuoteRepository $quoteRepository;

    /**
     * @param StringifyDbTableData $stringifyDbTableData
     * @param QuoteRepository $quoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        StringifyDbTableData $stringifyDbTableData,
        QuoteRepository $quoteRepository
    ) {
        $this->stringifyDbTableData = $stringifyDbTableData;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Collect data from the database and add it to the debug data object
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $this->setDataToDataObject($observer->getEvent()->getDebugDataObject());
    }

    /**
     * Set data to data object
     *
     * @param DebugDataObject $dataObject
     * @return void
     */
    protected function setDataToDataObject(DebugDataObject $dataObject): void
    {
        $data = $this->quoteRepository->getPaymentsQuotes(1000, 'DESC');
        $paymentsQuoteTableData = $this->stringifyDbTableData->getStringData($data);

        $dataObject->addData('klarna_kp', $paymentsQuoteTableData);
    }
}
