<?php
declare(strict_types=1);

namespace Lkg\StockSync\Service;

use Lkg\Base\Api\SyncStatusInterface;
use Lkg\StockSync\Helper\Data as StockSyncHelper;
use Lkg\StockSync\Logger\Logger;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Session\SessionManagerInterface;
use Lkg\StockSync\Service\InsertCronHistory;
use Lkg\Base\Helper\Data as HelperBaseLkg;

class PublisherStockSyncProcessor
{
    /**
     * @var SaveStockSyncData
     */
    protected SaveStockSyncData $saveStockSyncData;

    /**
     * @var UpdateProductAvailabilityStatus
     */
    protected UpdateProductAvailabilityStatus $updateProductAvailabilityStatus;

    /**
     * @var UpdateStock
     */
    protected UpdateStock $updateStock;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var HandlerStockSync
     */
    protected HandlerStockSync $handlerStockSync;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var StockRegistryInterface
     */
    protected StockRegistryInterface $stockRegistry;

    /**
     * @var StockSyncHelper
     */
    protected StockSyncHelper $stockSyncHelper;

    /**
     * @var SessionManagerInterface
     */
    protected SessionManagerInterface $session;

    /**
     * @var Logger
     */
    protected Logger $logger;

    /**
     * @var InsertCronHistory
     */
    protected InsertCronHistory $insertCronHistory;

    /**
     * @var HelperBaseLkg
     */
    protected HelperBaseLkg $helperBaseLkg;

    /**
     * @param HandlerStockSync $handlerStockSync
     * @param ProductRepositoryInterface $productRepository
     * @param StockSyncHelper $stockSyncHelper
     * @param logger $logger
     * @param SaveStockSyncData $saveStockSyncData
     * @param UpdateProductAvailabilityStatus $updateProductAvailabilityStatus
     * @param UpdateStock $updateStock
     * @param SessionManagerInterface $session
     * @param InsertCronHistory $insertCronHistory
     * @param HelperBaseLkg $helperBaseLkg
     */
    public function __construct(
        HandlerStockSync                $handlerStockSync,
        ProductRepositoryInterface      $productRepository,
        StockSyncHelper                 $stockSyncHelper,
        Logger                          $logger,
        SaveStockSyncData               $saveStockSyncData,
        UpdateProductAvailabilityStatus $updateProductAvailabilityStatus,
        UpdateStock                     $updateStock,
        SessionManagerInterface         $session,
        InsertCronHistory               $insertCronHistory,
        HelperBaseLkg                   $helperBaseLkg
    )
    {
        $this->handlerStockSync = $handlerStockSync;
        $this->productRepository = $productRepository;
        $this->stockSyncHelper = $stockSyncHelper;
        $this->logger = $logger;
        $this->saveStockSyncData = $saveStockSyncData;
        $this->updateProductAvailabilityStatus = $updateProductAvailabilityStatus;
        $this->updateStock = $updateStock;
        $this->session = $session;
        $this->insertCronHistory = $insertCronHistory;
        $this->helperBaseLkg = $helperBaseLkg;
    }

    /**
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function handlerPublisherStockSyncProcessor(): void
    {
        $publisherIds = trim($this->stockSyncHelper->publisherId());
        if ($publisherIds !== '') {
            $publisherIds = explode(',', $publisherIds);
            foreach ($publisherIds as $publisherId)
            {
                $publisherId = trim($publisherId);
                if ($publisherId !== '') {
                    $stockInfo = $this->handlerStockSync->processWsdlUrl(
                        [
                            'verlag' => $publisherId,
                            'ziel' => $this->stockSyncHelper->soapTarget()
                        ]
                    );

                    $lstItems = '';

                    foreach ($stockInfo as $stockItem) {

                        if((int)$this->helperBaseLkg->isStatusHistoryCron())
                        {
                            $lstItems = $lstItems . $stockItem->asXML();
                        }

                        $sku = (string)$stockItem->article_number;
                        $qty = (int)$stockItem->stock_available;
                        try {
                            $product = $this->productRepository->get($sku);

                            $publishingYear = empty($product->getData('publishing_year')) ? '' : $product->getData('publishing_year');
                            $this->updateProductAvailabilityStatus->handleUpdateAvailabilityStatus($product, $qty, $publishingYear);
                            $this->updateStock->handleStockUpdate($sku, $qty);
                            $this->saveStockSyncData->handleSaveSyncData($stockItem, [
                                'publisher_id' => $publisherId,
                                'status' => SyncStatusInterface::SUCCESS
                            ]);

                        } catch (NoSuchEntityException $e) {
                            $this->logger->info("Product with SKU $sku not found.");
                            $this->saveStockSyncData->handleSaveSyncData($stockItem, [
                                'publisher_id' => $publisherId,
                                'status' => SyncStatusInterface::FAIL,
                                'message' => $e->getMessage()
                            ]);
                            continue;
                        }
                    }

                    if($this->session->getData('is_cron_stock_sync'))
                    {
                        $this->insertCronHistory->handlerInsertCronHistory(
                            [
                                'job_code' => 'lgk_sync_stock_job',
                                'request' => [
                                    'verlag' => $publisherId,
                                    'ziel' => $this->stockSyncHelper->soapTarget()
                                ],
                                'response' => $lstItems,
                            ]
                        );
                    }
                }
            }
        }
    }
}
