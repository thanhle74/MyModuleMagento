<?php
declare(strict_types=1);
namespace Annam\MealPlan\Api;

use Annam\MealPlan\Api\MealInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface MealRepositoryInterface
{
    /**
     * @param \Annam\MealPlan\Api\MealInterface $meal
     * @return mixed
     */
    public function save(MealInterface $meal);

    /**
     * @param MealInterface $meal
     * @return mixed
     */
    public function delete(MealInterface $meal);

    /**
     * @param int $id
     * @return mixed
     */
    public function getById(int $id);

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
