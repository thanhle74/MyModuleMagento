<?php
declare(strict_types=1);
namespace Annam\Ingredient\Model\ResourceModel\Ingredient;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Annam\Ingredient\Model\Ingredient',
            'Annam\Ingredient\Model\ResourceModel\Ingredient'
        );
    }
}
