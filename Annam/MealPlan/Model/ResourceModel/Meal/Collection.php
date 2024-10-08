<?php
declare(strict_types=1);
namespace Annam\MealPlan\Model\ResourceModel\Meal;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Annam\MealPlan\Model\Meal',
            'Annam\MealPlan\Model\ResourceModel\Meal'
        );
    }
}
