<?php
declare(strict_types=1);
namespace Annam\MealPlan\Controller\Adminhtml\Plan;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Meal Plan'));
        return $resultPage;
    }
}
