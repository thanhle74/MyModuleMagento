<?php
declare(strict_types=1);
namespace Annam\Insights\Controller\Adminhtml\View;

use Annam\Insights\Model\Insights;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;

class Delete extends Action
{
    /**
     * @var Insights
     */
    protected $insights;

    /**
     * @param Context $context
     * @param Insights $insights
     */
    public function __construct(
        Action\Context $context,
        Insights $insights
    ) {
        parent::__construct($context);
        $this->insights = $insights;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Annam_Insights::index_delete');
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
                $model = $this->insights;
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
