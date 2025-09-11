<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder\Nodes;

use Klarna\Kp\Model\Api\Request\Builder;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Framework\DataObjectFactory;

/**
 * @internal
 */
class MerchantReferences
{
    /**
     * @var DataObjectFactory
     */
    private DataObjectFactory $dataObjectFactory;

    /**
     * @param DataObjectFactory $dataObjectFactory
     * @codeCoverageIgnore
     */
    public function __construct(DataObjectFactory $dataObjectFactory)
    {
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Adding the merchant references to the request
     *
     * @param Builder $requestBuilder
     * @param CartInterface $magentoQuote
     */
    public function addToRequest(Builder $requestBuilder, CartInterface $magentoQuote): void
    {
        \file_put_contents('/tmp/gabor.log', "\n".__METHOD__, FILE_APPEND);
        \file_put_contents('/tmp/gabor.log', "\n"."  \$magentoQuote->getReservedOrderId() = ".var_export($magentoQuote->getReservedOrderId(), true), FILE_APPEND);


        $dataObject = $this->dataObjectFactory->create([
            'data' => [
                'merchant_reference_1' => $magentoQuote->getReservedOrderId(),
                'merchant_reference_2' => ''
            ]
        ]);

        $requestBuilder->setMerchantReferences($dataObject);
    }
}
