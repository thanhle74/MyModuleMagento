<?php
declare(strict_types=1);
namespace TTTech\HistoryImport\Model\ResourceModel\HistoryImport;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use TTTech\HistoryImport\Model\HistoryImport as Model;
use TTTech\HistoryImport\Model\ResourceModel\HistoryImport as ResourceModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(
            'TTTech\HistoryImport\Model\HistoryImport',
            'TTTech\HistoryImport\Model\ResourceModel\HistoryImport'
        );
    }
}
