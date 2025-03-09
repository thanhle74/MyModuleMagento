<?php
declare(strict_types=1);
namespace TTTech\HistoryImport\Controller\Adminhtml\History;

use Magento\Framework\Controller\ResultFactory;

class View extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('History Import Item'));
        return $resultPage;
    }
}
