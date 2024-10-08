<?php
declare(strict_types=1);
namespace Annam\Dish\Controller\Adminhtml\View;

use Annam\Dish\Model\Dish;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;

class Delete extends Action
{
    /**
     * @var Dish
     */
    protected Dish $modelDish;

    /**
     * @param Context $context
     * @param Dish $modelDish
     */
    public function __construct(
        Action\Context $context,
        Dish $modelDish
    ) {
        parent::__construct($context);
        $this->modelDish = $modelDish;
    }

    /**
     * Summary of _isAllowed
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Annam_Dish::index_delete');
    }

    /**
     * Summary of execute
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute(): Redirect
    {
        $id = $this->getRequest()->getParam('id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->modelDish;
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
