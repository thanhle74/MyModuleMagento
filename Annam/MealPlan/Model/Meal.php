<?php
declare(strict_types=1);
namespace Annam\MealPlan\Model;

use Magento\Framework\Model\AbstractModel;
use Annam\MealPlan\Api\MealInterface;

class Meal extends AbstractModel implements MealInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Annam\MealPlan\Model\ResourceModel\Meal');
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }
}
