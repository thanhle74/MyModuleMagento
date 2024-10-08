<?php
declare(strict_types=1);
namespace Annam\Insights\Controller\Adminhtml\View;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Annam\Insights\Model\ResourceModel\Insights\CollectionFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Annam\Insights\Model\Insights;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @var Insights
     */
    protected Insights $insights;

    /**
     * @var Filter
     */
    protected Filter $filter;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Insights $insights
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Insights $insights,
        Filter $filter
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->insights = $insights;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $delete = 0;
        foreach ($collection as $model) {
            $this->insights->load((int)$model->getId())->delete();
            $delete++;
        }

        $this->messageManager->addSuccess(__('A total of %1 Records have been deleted.', $delete));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
