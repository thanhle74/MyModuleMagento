<?php
declare(strict_types=1);
namespace Annam\Dish\Model\ResourceModel\Dish;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Annam\Dish\Model\Dish',
            'Annam\Dish\Model\ResourceModel\Dish'
        );
    }
}
