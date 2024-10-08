<?php
declare(strict_types=1);
namespace Annam\Ingredient\Controller\Adminhtml\View;

use Annam\Ingredient\Model\Ingredient;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;

class Delete extends Action
{
    /**
     * @var Ingredient
     */
    protected Ingredient $ingredient;

    /**
     * @param Context $context
     * @param Ingredient $ingredient
     */
    public function __construct(
        Action\Context $context,
        Ingredient $ingredient
    ) {
        parent::__construct($context);
        $this->ingredient = $ingredient;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Annam_Ingredient::index_delete');
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->ingredient;
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
