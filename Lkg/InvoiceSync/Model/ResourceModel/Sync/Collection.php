<?php

/**
 * @author ICT
 * @copyright Copyright (c) 2024 Juni.com
 * @package A Magento 2 Invoice SYNC To LKG System Module.
 */

namespace Lkg\InvoiceSync\Model\ResourceModel\Sync;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Lkg\InvoiceSync\Model\Sync as SyncModel;
use Lkg\InvoiceSync\Model\ResourceModel\Sync as SyncResourceModel;

/**
 * Collection of Lkg InvoiceSync Plan
 */

class Collection extends AbstractCollection
{
    /**
     * Initialize collection
     *
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _construct()
    {
        $this->_init(SyncModel::class, SyncResourceModel::class);
    }
    // @codingStandardsIgnoreEnd
}
