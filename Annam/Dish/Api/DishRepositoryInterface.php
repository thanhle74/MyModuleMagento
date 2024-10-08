<?php
declare(strict_types=1);
namespace Annam\Dish\Api;

use Annam\Dish\Api\DishInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface DishRepositoryInterface
{
    /**
     * @param DishInterface $dish
     * @return mixed
     */
    public function save(DishInterface $dish);

    /**
     * @param DishInterface $dish
     * @return mixed
     */
    public function delete(DishInterface $dish);

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
