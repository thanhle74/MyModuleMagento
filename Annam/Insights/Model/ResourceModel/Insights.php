<?php
declare(strict_types=1);
namespace Annam\Insights\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Insights extends AbstractDb
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
        $this->_init('healthlab_insights', 'id');
    }
}
