<?php
declare(strict_types=1);
namespace Annam\Dish\Controller\Adminhtml\View;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Add New Dish'));
        return $resultPage;
    }
}
