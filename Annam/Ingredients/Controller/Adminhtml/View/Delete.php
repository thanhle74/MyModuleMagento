<?php
declare(strict_types=1);
namespace Annam\Ingredients\Controller\Adminhtml\View;

use Annam\Ingredients\Model\Ingredients;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;

class Delete extends Action
{
    /**
     * @var Ingredients
     */
    protected Ingredients $ingredients;

    /**
     * @param Context $context
     * @param Ingredients $ingredients
     */
    public function __construct(
        Action\Context $context,
        Ingredients $ingredients
    ) {
        parent::__construct($context);
        $this->ingredients = $ingredients;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Annam_Ingredients::index_delete');
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
                $model = $this->ingredients;
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
