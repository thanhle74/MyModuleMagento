<?php
declare(strict_types=1);
namespace Lkg\Base\Api;

use Lkg\Base\Api\Data\CronHistoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface CronHistoryRepositoryInterface
{
    /**
     * @param CronHistoryInterface $cronHistory
     * @return mixed
     */
    public function save(CronHistoryInterface $cronHistory);

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * @param CronHistoryInterface $cronHistory
     * @return mixed
     */
    public function delete(CronHistoryInterface $cronHistory);

    /**
     * @param $id
     * @return mixed
     */
    public function deleteById($id);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
