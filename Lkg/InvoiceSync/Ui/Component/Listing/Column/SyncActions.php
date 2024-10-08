<?php

/**
 * @author ICT
 * @copyright Copyright (c) 2024 Juni.com
 * @package A Magento 2 Invoice SYNC To LKG System Module.
 */

namespace Lkg\InvoiceSync\Ui\Component\Listing\Column;

use Lkg\InvoiceSync\Model\Sync;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class SyncActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    public const URL_INVOICESYNC_RESUBMIT = 'invoicesync/invoicesync/resubmit';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Sync
     */
    protected $sync;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoicerepositoryinterface;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchcriteriabuilder;

    /**
     * Constructor for the class.
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder URL builder for generating URLs.
     * @param Sync $sync Custom sync service for handling synchronization logic.
     * @param InvoiceRepositoryInterface $invoicerepositoryinterface Repository interface for handling invoices.
     * @param SearchCriteriaBuilder $searchcriteriabuilder Builder for search criteria objects.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context Context interface for UI components.
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory Factory for creating UI components.
     * @param array $components Optional array of components for the UI component.
     * @param array $data Optional array of data for the UI component.
     */

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        Sync $sync,
        InvoiceRepositoryInterface $invoicerepositoryinterface,
        SearchCriteriaBuilder $searchcriteriabuilder,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->sync = $sync;
        $this->invoicerepositoryinterface = $invoicerepositoryinterface;
        $this->searchcriteriabuilder = $searchcriteriabuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

     /**
      * Prepare data source
      *
      * @param array $dataSource
      * @return array
      */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item['sync_status'] == "Failed") {
                    if (isset($item['sync_id'])) {
                        $item[$this->getData('name')] = [
                            'edit' => [
                                'href' => $this->urlBuilder->getUrl(
                                    static::URL_INVOICESYNC_RESUBMIT,
                                    [
                                        'sync_id' => $item['invoice_id'
                                        ],
                                    ]
                                ),
                                'label' => __('Resubmit'),
                            ],
                        ];
                    }
                }
                $invoice = $this->loadInvoiceByOrderId($item['sync_order_id']);
                if ($invoice) {
                    $invoiceId = $invoice->getId();
                    $invoiceIncrementId = $invoice->getIncrementId();
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $sync = $this->sync->load($item['sync_order_id'], 'sync_order_id');
                    $sync->setInvoiceId($invoiceId);
                    $sync->setInvoiceIncrementId($invoiceIncrementId)->save();
                }
            }
        }
        return $dataSource;
    }

    /**
     * Get Invoice Data
     *
     * @param array $orderId
     * @return array
     */
    protected function loadInvoiceByOrderId($orderId)
    {
        try {
            $invoooo = $this->invoicerepositoryinterface;
            $search = $this->searchcriteriabuilder;
            $invoiceCollection = $invoooo->getList($search->addFilter('order_id', $orderId)->create());
            $invoices = $invoiceCollection->getItems();
            // Assuming only one invoice per order
            return reset($invoices);
        } catch (\Exception $e) {
            // Handle the exception
            return null;
        }
    }
}
