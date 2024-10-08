<?php
declare(strict_types=1);
namespace Annam\Ingredients\Model;

use Magento\Framework\Model\AbstractModel;

class Ingredients extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Annam\Ingredients\Model\ResourceModel\Ingredients');
    }
}
