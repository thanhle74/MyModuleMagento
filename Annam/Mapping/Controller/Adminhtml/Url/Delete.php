<?php
declare(strict_types=1);
namespace Annam\Mapping\Controller\Adminhtml\Url;

use Annam\Mapping\Model\Mapping;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;

class Delete extends Action
{
    /**
     * @var Mapping
     */
    protected Mapping $mapping;

    /**
     * @param Context $context
     * @param Insights $insights
     */
    public function __construct(
        Action\Context $context,
        Mapping $mapping
    ) {
        parent::__construct($context);
        $this->mapping = $mapping;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Annam_Mapping::index_delete');
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
                $model = $this->mapping;
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
