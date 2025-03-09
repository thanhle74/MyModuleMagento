<?php
declare(strict_types=1);
namespace TTTech\HistoryImport\Controller\Adminhtml\History;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use TTTech\HistoryImport\Model\ResourceModel\HistoryImport\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected Filter $filter;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return Redirect
     * @throws LocalizedException|Exception
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $count = 0;
            foreach ($collection as $model)
            {
                $model->delete();
                $count++;
            }
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $count));
        } catch (Exception $e)
        {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
