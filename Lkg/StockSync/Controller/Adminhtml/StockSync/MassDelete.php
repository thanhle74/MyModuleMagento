<?php
declare(strict_types=1);

namespace Lkg\StockSync\Controller\Adminhtml\StockSync;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Lkg\StockSync\Model\ResourceModel\StockSync\CollectionFactory;
use Lkg\StockSync\Api\StockSyncRepositoryInterface;

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
     * @var StockSyncRepositoryInterface
     */
    protected StockSyncRepositoryInterface $stockSyncRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param StockSyncRepositoryInterface $stockSyncRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        StockSyncRepositoryInterface $stockSyncRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->stockSyncRepository = $stockSyncRepository;
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
                $this->stockSyncRepository->delete($model);
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
