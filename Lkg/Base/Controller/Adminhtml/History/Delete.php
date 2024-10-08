<?php
declare(strict_types=1);
namespace Lkg\Base\Controller\Adminhtml\History;

use Lkg\Base\Model\CronHistory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;

class Delete extends Action
{
    /**
     * @var CronHistory
     */
    protected CronHistory $modelCronHistory;

    /**
     * @param Context $context
     * @param CronHistory $modelCronHistory
     */
    public function __construct(
        Action\Context $context,
        CronHistory $modelCronHistory
    ) {
        parent::__construct($context);
        $this->modelCronHistory = $modelCronHistory;
    }

    /**
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Lkg_Base::index_delete');
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
                $model = $this->modelCronHistory;
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
