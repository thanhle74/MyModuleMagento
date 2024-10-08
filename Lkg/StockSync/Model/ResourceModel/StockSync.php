<?php
declare(strict_types=1);
namespace Lkg\StockSync\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class StockSync extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lkg_stock_sync', 'sync_id');
    }
}
