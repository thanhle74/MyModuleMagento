<?php
declare(strict_types=1);
namespace Annam\Dish\Model;

use Magento\Framework\Model\AbstractModel;
use Annam\Dish\Api\DishInterface;

class Dish extends AbstractModel implements DishInterface
{
    protected function _construct()
    {
        $this->_init('Annam\Dish\Model\ResourceModel\Dish');
    }

    /**
     * @return array|mixed|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }
}
