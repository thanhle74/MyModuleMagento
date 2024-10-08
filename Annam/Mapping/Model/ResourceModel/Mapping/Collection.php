<?php
declare(strict_types=1);
namespace Annam\Mapping\Model\ResourceModel\Mapping;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Annam\Mapping\Model\Mapping',
            'Annam\Mapping\Model\ResourceModel\Mapping'
        );
    }
}
