<?php
declare(strict_types=1);

namespace TTTech\Import\Service;

use Exception;
use TTTech\Import\Helper\Data as HelperData;
use TTTech\HistoryImport\Model\ResourceModel\HistoryImport\CollectionFactory as HistoryImportCollectionFactory;
use TTTech\HistoryImport\Model\ResourceModel\HistoryImport as HistoryImportResource;
use TTTech\Import\Logger\Logger;

class DeleteOldRecords
{
    /**
     * Summary of logger
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Summary of helperData
     * @var HelperData
     */
    protected HelperData $helperData;

    /**
     * Summary of historyImportCollectionFactory
     * @var HistoryImportCollectionFactory
     */
    protected HistoryImportCollectionFactory $historyImportCollectionFactory;

    /**
     * Summary of historyImportResource
     * @var HistoryImportResource
     */
    protected HistoryImportResource $historyImportResource;

    /**
     * Summary of __construct
     * @param \TTTech\Import\Logger\Logger $logger
     * @param \TTTech\Import\Helper\Data $helperData
     * @param \TTTech\HistoryImport\Model\ResourceModel\HistoryImport\CollectionFactory $historyImportCollectionFactory
     * @param \TTTech\HistoryImport\Model\ResourceModel\HistoryImport $historyImportResource
     */
    public function __construct(
        Logger $logger,
        HelperData $helperData,
        HistoryImportCollectionFactory $historyImportCollectionFactory,
        HistoryImportResource $historyImportResource
    ) 
    {
        $this->logger = $logger;
        $this->helperData = $helperData;
        $this->historyImportCollectionFactory = $historyImportCollectionFactory;
        $this->historyImportResource = $historyImportResource;
    }

    /**
     * Summary of handlerDeleteOldRecords
     * @return void
     */
    public function handlerDeleteOldRecords(): void
    {
        $timeThreshold = $this->helperData->cleanupTimeThreshold();
        $date = (new \DateTime())->modify("-{$timeThreshold} hours")->format('Y-m-d H:i:s');

        $collection = $this->historyImportCollectionFactory->create();
        $collection->addFieldToFilter('created', ['lt' => $date]);

        foreach ($collection as $item) {
            try {
                $this->historyImportResource->delete($item);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
