<?php

/**
 * @author Zeo Team
 * @copyright Copyright (c) 2023 Zeo (https://www.zeo.nl/)
 * @package A Magento 2 Subscriptionplan Module.
 */

namespace Lkg\InvoiceSync\Controller\Adminhtml\InvoiceSync;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Lkg\InvoiceSync\Model\ResourceModel\Sync\CollectionFactory;
use Lkg\InvoiceSync\Model\Sync;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Sync
     */
    protected $sync;

    /**
     * MassDelete constructor.
     *
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     * @param Sync              $sync
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Sync $sync
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->sync = $sync;
        parent::__construct($context);
    }
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $count = 0;
            foreach ($collection as $model) {
                $model = $this->sync->load($model->getId());
                $model->delete();
                $count++;
            }
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $count));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
