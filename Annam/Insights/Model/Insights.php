<?php
declare(strict_types=1);
namespace Annam\Insights\Model;

use Magento\Framework\Model\AbstractModel;

class Insights extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Annam\Insights\Model\ResourceModel\Insights');
    }
}
