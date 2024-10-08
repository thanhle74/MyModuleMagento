<?php
declare(strict_types=1);

namespace Lkg\StockSync\Cron;

use Lkg\StockSync\Service\PublisherStockSyncProcessor;
use Lkg\StockSync\Service\DeleteOldRecords;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Lkg\StockSync\Helper\Data as StockSyncHelper;
use Magento\Framework\Session\SessionManagerInterface;

class SyncStockJob
{
    /**
     * @var PublisherStockSyncProcessor
     */
    protected PublisherStockSyncProcessor $publisherStockSyncProcessor;

    /**
     * @var DeleteOldRecords
     */
    protected DeleteOldRecords $deleteOldRecords;

    /**
     * @var StockSyncHelper
     */
    protected StockSyncHelper $stockSyncHelper;

    /**
     * @var SessionManagerInterface
     */
    protected SessionManagerInterface $session;

    /**
     * @param PublisherStockSyncProcessor $publisherStockSyncProcessor
     * @param DeleteOldRecords $deleteOldRecords
     * @param StockSyncHelper $stockSyncHelper
     * @param SessionManagerInterface $session
     */
    public function __construct
    (
        PublisherStockSyncProcessor $publisherStockSyncProcessor,
        DeleteOldRecords $deleteOldRecords,
        StockSyncHelper $stockSyncHelper,
        SessionManagerInterface $session
    )
    {
        $this->publisherStockSyncProcessor = $publisherStockSyncProcessor;
        $this->deleteOldRecords = $deleteOldRecords;
        $this->stockSyncHelper = $stockSyncHelper;
        $this->session = $session;
    }

    /**
     * @return void
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws StateException
     */
    public function execute(): void
    {
        if((int)$this->stockSyncHelper->statusModule())
        {
            $this->session->setData('is_cron_stock_sync', true);

            try {
                $this->publisherStockSyncProcessor->handlerPublisherStockSyncProcessor();
                $this->deleteOldRecords->handlerDeleteOldRecords();
            } finally {
                $this->session->unsetData('is_cron_stock_sync');
            }
        }
    }
}
