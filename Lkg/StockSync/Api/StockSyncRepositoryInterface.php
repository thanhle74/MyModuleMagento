<?php
declare(strict_types=1);
namespace Lkg\StockSync\Api;

use Lkg\StockSync\Api\Data\StockSyncInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface StockSyncRepositoryInterface
{
    /**
     * @param StockSyncInterface $stockSync
     * @return StockSyncInterface
     */
    public function save(StockSyncInterface $stockSync): StockSyncInterface;

    /**
     * @param int $id
     * @return StockSyncInterface
     */
    public function getById(int $id): StockSyncInterface;

    /**
     * @param StockSyncInterface $stockSync
     * @return bool
     */
    public function delete(StockSyncInterface $stockSync): bool;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById(int $id): bool;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
