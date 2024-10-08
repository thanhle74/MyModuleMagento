<?php
declare(strict_types=1);
namespace Lkg\StockSync\Service;

use Exception;
use Lkg\StockSync\Helper\Data as StockSyncHelper;
use Lkg\StockSync\Model\ResourceModel\StockSync\CollectionFactory as StockSyncCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Lkg\StockSync\Model\StockSyncRepository;

class DeleteOldRecords
{
    /**
     * @var StockSyncCollectionFactory
     */
    protected StockSyncCollectionFactory $stockSyncCollectionFactory;

    /**
     * @var StockSyncHelper
     */
    protected StockSyncHelper $stockSyncHelper;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var StockSyncRepository
     */
    protected StockSyncRepository $stockSyncRepository;

    /**
     * @param StockSyncCollectionFactory $stockSyncCollectionFactory
     * @param StockSyncHelper $stockSyncHelper
     * @param LoggerInterface $logger
     * @param StockSyncRepository $stockSyncRepository
     */
    public function __construct(
        StockSyncCollectionFactory $stockSyncCollectionFactory,
        StockSyncHelper $stockSyncHelper,
        LoggerInterface $logger,
        StockSyncRepository $stockSyncRepository
    ) {
        $this->stockSyncCollectionFactory = $stockSyncCollectionFactory;
        $this->stockSyncHelper = $stockSyncHelper;
        $this->logger = $logger;
        $this->stockSyncRepository = $stockSyncRepository;
    }

    /**
     * @return void
     * @throws NoSuchEntityException
     */
    public function handlerDeleteOldRecords(): void
    {
        $timeThreshold = $this->stockSyncHelper->cleanupTimeThreshold();
        $date = (new \DateTime())->modify("-{$timeThreshold} hours")->format('Y-m-d H:i:s');

        $collection = $this->stockSyncCollectionFactory->create();
        $collection->addFieldToFilter('created', ['lt' => $date]);

        foreach ($collection as $item) {
            try {
                $this->stockSyncRepository->delete($item);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
