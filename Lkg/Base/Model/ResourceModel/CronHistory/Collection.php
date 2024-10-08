<?php
declare(strict_types=1);
namespace Lkg\Base\Model\ResourceModel\CronHistory;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Lkg\Base\Model\CronHistory as Model;
use Lkg\Base\Model\ResourceModel\CronHistory as ResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}

