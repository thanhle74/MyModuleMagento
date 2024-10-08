<?php
declare (strict_types = 1);
namespace Annam\Ingredient\Ui\Component\Ingredient;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Annam\HealthLab\Helper\Data as AnnamHelper;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;

class Products implements OptionSourceInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var AnnamHelper
     */
    protected AnnamHelper $annamHelper;

    /**
     * @var SortOrderBuilder
     */
    protected SortOrderBuilder $sortOrderBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param AnnamHelper $annamHelper
     * @param SortOrderBuilder $sortOrderBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     */
    public function __construct
    (
        ProductRepositoryInterface $productRepository,
        AnnamHelper $annamHelper,
        SortOrderBuilder $sortOrderBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    )
    {
        $this->productRepository = $productRepository;
        $this->annamHelper = $annamHelper;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $data = [];
        try {
            $attributeCode = $this->annamHelper->getAttributeInfographic();
            $sortOrder = $this->sortOrderBuilder->setField('name')->setDirection(DataCollection::SORT_ORDER_ASC)->create();
            $this->searchCriteriaBuilder->addFilter($attributeCode, 1);
            $this->searchCriteriaBuilder->addFilter('status' , Status::STATUS_ENABLED);
            $this->searchCriteriaBuilder->addFilter('visibility' , Visibility::VISIBILITY_NOT_VISIBLE, 'neq');
            $searchCriteria = $this->searchCriteriaBuilder->addSortOrder($sortOrder)->create();
            $products = $this->productRepository->getList($searchCriteria)->getItems();
            if(count($products))
            {
                foreach ($products as $product)
                {
                    $data[] = [
                        "value" => $product->getSku(),
                        "label" => $product->getName(),
                    ];
                }
            }
        }catch (\Exception $e)
        {
            $this->logger->info($e->getMessage());
        }

        return $data;
    }
}
