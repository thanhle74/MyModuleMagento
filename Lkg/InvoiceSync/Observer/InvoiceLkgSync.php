<?php

/**
 * @author ICT
 * @copyright Copyright (c) 2024 Juni.com
 * @package A Magento 2 Invoice SYNC To LKG System Module.
 */

namespace Lkg\InvoiceSync\Observer;

use Magento\Framework\Event\ObserverInterface;

class InvoiceLkgSync implements ObserverInterface
{
    /**
     * @var SyncInvoice
     */
    protected $syncInvoice;

    /**
     * Constructor for the class.
     *
     * @param SyncInvoice $syncInvoice Instance for managing order invoice.
     */
    public function __construct(
        \Lkg\InvoiceSync\Model\SyncInvoice $syncInvoice
    ) {
        $this->syncInvoice = $syncInvoice;
    }

    /**
     * Constructor for the class.
     *
     * @param Observer $observer Instance for managing Invoice Data only.
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer->getData('invoice');
        $this->syncInvoice->processInvoiceData($invoice);
    }
}
