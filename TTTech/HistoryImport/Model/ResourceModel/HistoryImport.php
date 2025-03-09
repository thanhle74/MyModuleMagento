<?php
declare(strict_types=1);
namespace TTTech\HistoryImport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class HistoryImport extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('tttech_history', 'id');
    }
}
