<?php

/**
 * @author ICT
 * @copyright Copyright (c) 2024 Juni.com
 * @package A Magento 2 Invoice SYNC To LKG System Module.
 */

namespace Lkg\InvoiceSync\Controller\Adminhtml\InvoiceSync;

use Magento\Backend\App\Action;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Lkg\InvoiceSync\Model\SyncInvoice;
use Magento\Backend\App\Action\Context;

class Resubmit extends Action
{
    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoicerepositoryinterface;

    /**
     * @var SyncInvoice
     */
    protected $syncInvoice;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param InvoiceRepositoryInterface $invoicerepositoryinterface
     * @param SyncInvoice $syncInvoice
     */
    public function __construct(
        Context $context,
        InvoiceRepositoryInterface $invoicerepositoryinterface,
        SyncInvoice $syncInvoice
    ) {
       
        $this->invoicerepositoryinterface = $invoicerepositoryinterface;
        $this->syncInvoice = $syncInvoice;
        parent::__construct($context);
    }
        
    /**
     * Check if action is allowed
     *
     * @return bool
     */

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */

    public function execute()
    {
        $id = $this->getRequest()->getParam('sync_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $invoice = $this->invoicerepositoryinterface->get($id);
                $modal = $this->syncInvoice->processInvoiceData($invoice);
                $this->messageManager->addSuccess(__('Record Resubmitted successfully.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('Record does not exist.'));
        return $resultRedirect->setPath('*/*/');
    }
}
