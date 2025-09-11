<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\ResourceModel\Quote;

use Klarna\Kp\Model\Quote;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @internal
 */
class Collection extends AbstractCollection
{
    /**
     * Constructor
     *
     * @codeCoverageIgnore
     * @codingStandardsIgnoreLine
     */
    protected function _construct(): void
    {
        $this->_init(Quote::class, \Klarna\Kp\Model\ResourceModel\Quote::class);
    }
}
