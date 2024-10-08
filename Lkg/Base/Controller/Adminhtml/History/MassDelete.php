<?php
declare(strict_types=1);

namespace Lkg\Base\Controller\Adminhtml\History;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Lkg\Base\Model\ResourceModel\CronHistory\CollectionFactory;
use Lkg\Base\Api\CronHistoryRepositoryInterface;

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
     * @var CronHistoryRepositoryInterface
     */
    protected CronHistoryRepositoryInterface $cronHistoryRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CronHistoryRepositoryInterface $cronHistoryRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CronHistoryRepositoryInterface $cronHistoryRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->cronHistoryRepository = $cronHistoryRepository;
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
                $this->cronHistoryRepository->delete($model);
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
