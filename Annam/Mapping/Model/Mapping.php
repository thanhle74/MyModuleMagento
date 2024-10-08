<?php
declare(strict_types=1);
namespace Annam\Mapping\Model;

use Magento\Framework\Model\AbstractModel;

class Mapping extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Annam\Mapping\Model\ResourceModel\Mapping');
    }
}
