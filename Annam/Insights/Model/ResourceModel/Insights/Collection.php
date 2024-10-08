<?php
declare(strict_types=1);
namespace Annam\Insights\Model\ResourceModel\Insights;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Annam\Insights\Model\Insights',
            'Annam\Insights\Model\ResourceModel\Insights'
        );
    }
}
