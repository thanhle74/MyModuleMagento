<?php
declare(strict_types=1);
namespace Annam\HealthLab\Model;

use Annam\HealthLab\Api\ProductAttributeValuesGetterInterface;
use Annam\HealthLab\Helper\Data as AnnamHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Repository as AttributeRepository;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;
use Annam\HealthLab\Model\ProductHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;

class ProductAttributeValuesGetter implements ProductAttributeValuesGetterInterface
{
    /**
     * @var AttributeRepository
     */
    protected AttributeRepository $attributeRepository;

    /**
     * @var ResourceConnection
     */
    protected ResourceConnection $resource;

    /**
     * @var SortOrderBuilder
     */
    protected SortOrderBuilder $sortOrderBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var AnnamHelper
     */
    protected AnnamHelper $annamHelper;

    /**
     * @var ProductHelper
     */
    protected ProductHelper $productHelper;

    /**
     * @param AnnamHelper $annamHelper
     * @param AttributeRepository $attributeRepository
     * @param ResourceConnection $resource
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductRepositoryInterface $productRepository
     * @param SortOrderBuilder $sortOrderBuilder
     * @param LoggerInterface $logger
     * @param ProductHelper $productHelper
     */
    public function __construct
    (
        AnnamHelper $annamHelper,
        AttributeRepository $attributeRepository,
        ResourceConnection $resource,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepositoryInterface $productRepository,
        SortOrderBuilder $sortOrderBuilder,
        LoggerInterface $logger,
        ProductHelper $productHelper
    )
    {
        $this->annamHelper = $annamHelper;
        $this->attributeRepository = $attributeRepository;
        $this->resource = $resource;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->productHelper = $productHelper;
    }

    /**
     * @param String $searchValue
     * @return array
     * @throws NoSuchEntityException
     */
    public function getAttributeValues(String $searchValue): array
    {
        $similarValues = [];

        if(trim($searchValue))
        {

            $connection = $this->resource->getConnection();
            $attributeId = $this->attributeRepository->get($this->annamHelper->searchByKeyword())->getId();
            $select = $connection->select()->from(['main_table' => $this->resource->getTableName('eav_attribute_option_value')], ['value','option_id'])
                ->join(
                    ['ao' => $this->resource->getTableName('eav_attribute_option')],
                    'main_table.option_id = ao.option_id',
                    []
                )
                ->where('ao.attribute_id = ?', $attributeId)
                ->where('main_table.value LIKE ?', '%' . $searchValue . '%');
            $similarValues = $connection->fetchAll($select);
        }

        return $similarValues;
    }

    /**
     * @param string $attributeCode
     * @param int $valueId
     * @param array $sort
     * @return array
     */
    public function getListProducts(string $attributeCode, int $valueId, array $sort = []): array
    {
        if(count($sort))
        {
            $type   =   $sort['type'];
            $sort   =   $sort['sort'] == 'asc' ? DataCollection::SORT_ORDER_ASC : DataCollection::SORT_ORDER_DESC;
        }else{
            $type = 'name';
            $sort = DataCollection::SORT_ORDER_ASC;
        }
        $valueId = '%' . $valueId . '%';
        $result   =   [];
        try {
            $attributeSetIdSortOrder = $this->sortOrderBuilder->setField($type)->setDirection($sort)->create();
            $this->searchCriteriaBuilder->addSortOrder($attributeSetIdSortOrder);
            $this->searchCriteriaBuilder->addFilter($attributeCode, $valueId , 'like');
            $this->searchCriteriaBuilder->addFilter('status' , Status::STATUS_ENABLED);
            $this->searchCriteriaBuilder->addFilter('visibility' , Visibility::VISIBILITY_NOT_VISIBLE, 'neq');
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $productList = $this->productRepository->getList($searchCriteria);
            foreach ($productList->getItems() as $product) {
                if($product->getId())
                {
                    $result[]   =   [
                        'id'    =>  $product->getId(),
                        'name'  =>  $product->getname(),
                        'image' =>  $this->productHelper->getUrlImage($product),
                        'price' =>  $product->getPrice()
                    ];
                }
            }
        }catch (\Exception $e)
        {
            $this->logger->info($e->getMessage());
        }

        return $result;
    }
}
