<?php
declare(strict_types=1);
namespace TTTech\HistoryImport\Model;

use Magento\Framework\Model\AbstractModel;

class HistoryImport extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('TTTech\HistoryImport\Model\ResourceModel\HistoryImport');
    }
}
