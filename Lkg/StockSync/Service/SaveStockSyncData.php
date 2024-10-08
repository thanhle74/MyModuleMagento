<?php
declare(strict_types=1);

namespace Lkg\StockSync\Service;

use Lkg\StockSync\Model\StockSyncFactory;
use Lkg\StockSync\Api\StockSyncRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;

class SaveStockSyncData
{
    /**
     * @var StockSyncFactory
     */
    protected StockSyncFactory $stockSyncFactory;

    /**
     * @var StockSyncRepositoryInterface
     */
    protected StockSyncRepositoryInterface $stockSyncRepository;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param StockSyncFactory $stockSyncFactory
     * @param StockSyncRepositoryInterface $stockSyncRepository
     * @param LoggerInterface $logger
     */
    public function __construct
    (
        StockSyncFactory             $stockSyncFactory,
        StockSyncRepositoryInterface $stockSyncRepository,
        LoggerInterface              $logger
    )
    {
        $this->stockSyncFactory = $stockSyncFactory;
        $this->stockSyncRepository = $stockSyncRepository;
        $this->logger = $logger;
    }

    /**
     * @param $stockItem
     * @param array $data
     * @return void
     */
    public function handleSaveSyncData($stockItem, array $data): void
    {
        $sku = (string)$stockItem->article_number;
        try {
            $stockSync = $this->stockSyncFactory->create();
            $stockSync->setPublisherId((string)$data['publisher_id']);
            $stockSync->setArticleNumber($sku);
            $stockSync->setStockAvailable((int)$stockItem->stock_available);
            $stockSync->setStock((int)$stockItem->stock);
            $stockSync->setDatedWithReservation((int)$stockItem->dated_with_reservation);
            $stockSync->setSyncStatus($data['status']);
            $stockSync->setCreated(date('Y-m-d H:i:s'));
            if (isset($data['message'])) {
                $stockSync->setMessage($data['message']);
            }
            $this->stockSyncRepository->save($stockSync);
        } catch (CouldNotSaveException $e) {
            $this->logger->error("Could not save stock sync data for SKU $sku: " . $e->getMessage());
        }
    }
}
