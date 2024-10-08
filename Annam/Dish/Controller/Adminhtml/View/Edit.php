<?php
declare(strict_types=1);
namespace Annam\Dish\Controller\Adminhtml\View;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Dish'));
        return $resultPage;
    }
}
