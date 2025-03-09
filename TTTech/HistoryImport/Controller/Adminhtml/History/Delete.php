<?php
declare(strict_types=1);
namespace TTTech\HistoryImport\Controller\Adminhtml\History;

use TTTech\HistoryImport\Model\HistoryImport;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;

class Delete extends Action
{
    /**
     * @var HistoryImport
     */
    protected HistoryImport $modelHistoryImport;

    /**
     * @param Context $context
     * @param HistoryImport $modelHistoryImport
     */
    public function __construct(
        Action\Context $context,
        HistoryImport $modelHistoryImport
    ) {
        parent::__construct($context);
        $this->modelHistoryImport = $modelHistoryImport;
    }

    /**
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('TTTech_HistoryImport::index_delete');
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $id = $this->getRequest()->getParam('id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->modelHistoryImport;
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
