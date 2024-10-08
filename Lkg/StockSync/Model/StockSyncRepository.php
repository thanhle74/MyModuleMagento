<?php
declare(strict_types=1);
namespace Lkg\StockSync\Model;

use Exception;
use Lkg\StockSync\Api\Data\StockSyncInterface;
use Lkg\StockSync\Api\StockSyncRepositoryInterface;
use Lkg\StockSync\Model\ResourceModel\StockSync as ResourceStockSync;
use Lkg\StockSync\Model\ResourceModel\StockSync\CollectionFactory as StockSyncCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

class StockSyncRepository implements StockSyncRepositoryInterface
{
    /**
     * @var ResourceStockSync
     */
    private ResourceStockSync $resource;

    /**
     * @var StockSyncFactory
     */
    private StockSyncFactory $stockSyncFactory;

    /**
     * @var StockSyncCollectionFactory
     */
    private StockSyncCollectionFactory $stockSyncCollectionFactory;

    /**
     * @var SearchResultsFactory
     */
    private SearchResultsFactory $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @param ResourceStockSync $resource
     * @param StockSyncFactory $stockSyncFactory
     * @param StockSyncCollectionFactory $stockSyncCollectionFactory
     * @param SearchResultsFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceStockSync $resource,
        StockSyncFactory $stockSyncFactory,
        StockSyncCollectionFactory $stockSyncCollectionFactory,
        SearchResultsFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->stockSyncFactory = $stockSyncFactory;
        $this->stockSyncCollectionFactory = $stockSyncCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @param StockSyncInterface $stockSync
     * @return StockSyncInterface
     * @throws CouldNotSaveException
     */
    public function save(StockSyncInterface $stockSync): StockSyncInterface
    {
        try {
            $this->resource->save($stockSync);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $stockSync;
    }

    /**
     * @param int $id
     * @return StockSyncInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id): StockSyncInterface
    {
        $stockSync = $this->stockSyncFactory->create();
        $this->resource->load($stockSync, $id);
        if (!$stockSync->getId()) {
            throw new NoSuchEntityException(__('The stock sync record with ID "%1" does not exist.', $id));
        }
        return $stockSync;
    }

    /**
     * @param StockSyncInterface $stockSync
     * @return bool
     * @throws Exception
     */
    public function delete(StockSyncInterface $stockSync): bool
    {
        $this->resource->delete($stockSync);
        return true;
    }

    /**
     * @param int $id
     * @return bool
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function deleteById(int $id): bool
    {
        $stockSync = $this->getById($id);
        return $this->delete($stockSync);
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $collection = $this->stockSyncCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
