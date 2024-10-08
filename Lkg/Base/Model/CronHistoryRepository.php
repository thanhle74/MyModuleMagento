<?php
declare(strict_types=1);
namespace Lkg\Base\Model;

use Lkg\Base\Api\CronHistoryRepositoryInterface;
use Lkg\Base\Api\Data\CronHistoryInterface;
use Lkg\Base\Model\ResourceModel\CronHistory as CronHistoryResource;
use Lkg\Base\Model\CronHistoryFactory;
use Lkg\Base\Model\ResourceModel\CronHistory\CollectionFactory as CronHistoryCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

class CronHistoryRepository implements CronHistoryRepositoryInterface
{
    /**
     * @var CronHistoryResource
     */
    protected CronHistoryResource $resource;

    /**
     * @var CronHistoryFactory
     */
    protected CronHistoryFactory $cronHistoryFactory;

    /**
     * @var CronHistoryCollectionFactory
     */
    protected CronHistoryCollectionFactory $cronHistoryCollectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    protected SearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected CollectionProcessorInterface $collectionProcessor;

    /**
     * @param CronHistoryResource $resource
     * @param CronHistoryFactory $cronHistoryFactory
     * @param CronHistoryCollectionFactory $cronHistoryCollectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        CronHistoryResource $resource,
        CronHistoryFactory $cronHistoryFactory,
        CronHistoryCollectionFactory $cronHistoryCollectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->cronHistoryFactory = $cronHistoryFactory;
        $this->cronHistoryCollectionFactory = $cronHistoryCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @param CronHistoryInterface $cronHistory
     * @return CronHistoryInterface
     * @throws CouldNotSaveException
     */
    public function save(CronHistoryInterface $cronHistory)
    {
        try {
            $this->resource->save($cronHistory);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $cronHistory;
    }

    /**
     * @param $id
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $cronHistory = $this->cronHistoryFactory->create();
        $this->resource->load($cronHistory, $id);
        if (!$cronHistory->getId()) {
            throw new NoSuchEntityException(__('CronHistory with id "%1" does not exist.', $id));
        }
        return $cronHistory;
    }

    /**
     * @param CronHistoryInterface $cronHistory
     * @return true
     * @throws CouldNotDeleteException
     */
    public function delete(CronHistoryInterface $cronHistory)
    {
        try {
            $this->resource->delete($cronHistory);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @param $id
     * @return true
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        $cronHistory = $this->getById($id);
        return $this->delete($cronHistory);
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface|mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->cronHistoryCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
