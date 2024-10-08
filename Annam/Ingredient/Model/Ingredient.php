<?php
declare(strict_types=1);
namespace Annam\Ingredient\Model;

use Magento\Framework\Model\AbstractModel;

class Ingredient extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Annam\Ingredient\Model\ResourceModel\Ingredient');
    }
}
