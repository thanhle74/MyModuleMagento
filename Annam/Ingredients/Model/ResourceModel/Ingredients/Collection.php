<?php
declare(strict_types=1);
namespace Annam\Ingredients\Model\ResourceModel\Ingredients;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Annam\Ingredients\Model\Ingredients',
            'Annam\Ingredients\Model\ResourceModel\Ingredients'
        );
    }
}
