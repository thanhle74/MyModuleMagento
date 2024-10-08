<?php

/**
 * @author ICT
 * @copyright Copyright (c) 2024 Juni.com
 * @package A Magento 2 Invoice SYNC To LKG System Module.
 */

namespace Lkg\InvoiceSync\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Sync extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _construct()
    {
        $this->_init('lkg_invoice_sync', 'sync_id');
    }
    // @codingStandardsIgnoreEnd
}
