<?php
declare(strict_types=1);
namespace Annam\Dish\Model;

use Annam\Dish\Api\DishRepositoryInterface;
use Annam\Dish\Model\DishFactory;
use Exception;
use Magento\Catalog\Api\Data\ProductSearchResultsInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory;
use Annam\Dish\Api\DishInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;

class DishRepository implements DishRepositoryInterface
{
    /**
     * @var \Annam\HealthLab\Model\DishFactory
     */
    protected $dishFactory;

    /**
     * @var ProductSearchResultsInterfaceFactory
     */
    protected ProductSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @param DishFactory $dishFactory
     * @param ProductSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        DishFactory $dishFactory,
        ProductSearchResultsInterfaceFactory $searchResultsFactory
    )
    {
        $this->dishFactory = $dishFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @param DishInterface $dish
     * @return DishInterface
     * @throws AlreadyExistsException
     */
    public function save(DishInterface $dish)
    {
        $dish->getResource()->save($dish);
        return $dish;
    }

    /**
     * @param DishInterface $dish
     * @return void
     * @throws Exception
     */
    public function delete(DishInterface $dish)
    {
        $dish->getResource()->delete($dish);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById(int $id)
    {
        $obj = $this->dishFactory->create();
        $obj->getResource()->load($obj, $id);
        if (! $obj->getId()) {
            throw new NoSuchEntityException(__('Unable to find My Entity with ID "%1"', $id));
        }
        return $obj;
    }

    /**
     * @param $id
     * @return void
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        $obj = $this->getById($id);
        $obj->delete();
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ProductSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResult = $this->searchResultsFactory->create();
        $collection = $this->dishFactory->create();
        $this->dishFactory->process($searchCriteria, $collection);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }
}
