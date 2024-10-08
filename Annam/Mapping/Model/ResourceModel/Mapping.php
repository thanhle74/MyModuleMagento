<?php
declare(strict_types=1);
namespace Annam\Mapping\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Mapping extends AbstractDb
{
    public function __construct(
        Context $context,
                $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
    }

    public function _construct()
    {
        $this->_init('healthlab_mapping_url', 'id');
    }
}
