<?php
declare(strict_types=1);
namespace Annam\MealPlan\Controller\Adminhtml\Plan;

use Annam\MealPlan\Model\Meal;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Delete extends Action
{
    /**
     * @var Meal
     */
    protected Meal $modelMeal;

    /**
     * @param Context $context
     * @param Meal $modelMeal
     */
    public function __construct(
        Action\Context $context,
        Meal $modelMeal
    ) {
        parent::__construct($context);
        $this->modelMeal = $modelMeal;
    }

    /**
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Annam_MealPlan::index_delete');
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->modelMeal;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('Record deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('Record does not exist'));
        return $resultRedirect->setPath('*/*/');
    }
}
