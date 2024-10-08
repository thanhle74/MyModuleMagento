<?php
declare(strict_types=1);
namespace Lkg\Base\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CronHistory extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cron_history', 'id');
    }
}
