<?php
declare(strict_types=1);
namespace Annam\MealPlan\Model;

use Annam\MealPlan\Api\MealRepositoryInterface;
use Annam\MealPlan\Model\MealFactory;
use Exception;
use Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory;
use Annam\MealPlan\Model\ResourceModel\Meal\CollectionFactory;
use Annam\MealPlan\Api\MealInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;

class MealRepository implements MealRepositoryInterface
{
    /**
     * @var \Annam\MealPlan\Model\MealFactory
     */
    protected \Annam\MealPlan\Model\MealFactory $mealFactory;

    /**
     * @var ProductSearchResultsInterfaceFactory
     */
    protected ProductSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $mealCollectionFactory;

    /**
     * @param \Annam\MealPlan\Model\MealFactory $mealFactory
     * @param ProductSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionFactory $mealCollectionFactory
     */
    public function __construct(
        MealFactory $mealFactory,
        ProductSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionFactory $mealCollectionFactory
    )
    {
        $this->mealFactory = $mealFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->mealCollectionFactory = $mealCollectionFactory;
    }

    /**
     * @param MealInterface $meal
     * @return MealInterface
     * @throws AlreadyExistsException
     */
    public function save(MealInterface $meal)
    {
        $meal->getResource()->save($meal);
        return $meal;
    }

    /**
     * @param MealInterface $meal
     * @return void
     * @throws Exception
     */
    public function delete(MealInterface $meal)
    {
        $meal->getResource()->delete($meal);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById(int $id)
    {
        $obj = $this->mealFactory->create();
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
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResult = $this->searchResultsFactory->create();
        $collection = $this->mealFactory->create();
        $this->mealFactory->process($searchCriteria, $collection);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }
}
