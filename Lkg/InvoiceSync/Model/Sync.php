<?php

/**
 * @author ICT
 * @copyright Copyright (c) 2024 Juni.com
 * @package A Magento 2 Invoice SYNC To LKG System Module.
 */

namespace Lkg\InvoiceSync\Model;

use Magento\Framework\Model\AbstractModel;
use Lkg\InvoiceSync\Model\ResourceModel\Sync as SyncResourceModel;

class Sync extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Define resource model
     */
    // @codingStandardsIgnoreStart
    protected function _construct()
    {
        $this->_init(SyncResourceModel::class);
    }
    // @codingStandardsIgnoreEnd
}
