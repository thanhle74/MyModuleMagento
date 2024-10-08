<?php
declare(strict_types=1);
namespace Lkg\Base\Controller\Adminhtml\History;

use Magento\Framework\Controller\ResultFactory;

class View extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Cron History Item'));
        return $resultPage;
    }
}
