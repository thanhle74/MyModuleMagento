<?php
declare(strict_types=1);
namespace Lkg\StockSync\Model\ResourceModel\StockSync;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Lkg\StockSync\Model\StockSync as StockSyncModel;
use Lkg\StockSync\Model\ResourceModel\StockSync as StockSyncResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(StockSyncModel::class, StockSyncResourceModel::class);
    }
}
