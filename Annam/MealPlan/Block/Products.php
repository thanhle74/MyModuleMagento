<?php
declare(strict_types=1);
namespace Annam\MealPlan\Block;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Model\Config;
use Psr\Log\LoggerInterface;
use Magento\Framework\View\Element\Template;
use Annam\HealthLab\Helper\Data as AnnamHelper;
use Annam\Dish\Api\DishRepositoryInterface;
use Annam\MealPlan\Api\MealRepositoryInterface;

class Products extends Template
{
    /**
     * @var SearchCriteriaBuilder
     */
    public SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var ProductRepositoryInterface
     */
    public ProductRepositoryInterface $productRepository;

    /**
     * @var Config
     */
    public Config $eavConfig;

    /**
     * @var LoggerInterface
     */
    public LoggerInterface $logger;

    /**
     * @var DishRepositoryInterface
     */
    public DishRepositoryInterface $dishRepository;

    /**
     * @var AnnamHelper
     */
    public AnnamHelper $annamHelper;

    /**
     * @var MealRepositoryInterface
     */
    public MealRepositoryInterface $mealRepository;

    /**
     * @param LoggerInterface $logger
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Config $eavConfig
     * @param ProductRepositoryInterface $productRepository
     * @param Template\Context $context
     * @param DishRepositoryInterface $dishRepository
     * @param MealRepositoryInterface $mealRepository
     * @param AnnamHelper $annamHelper
     * @param array $data
     */
    public function __construct(
        LoggerInterface $logger,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $eavConfig,
        ProductRepositoryInterface $productRepository,
        Template\Context $context,
        DishRepositoryInterface $dishRepository,
        MealRepositoryInterface $mealRepository,
        AnnamHelper $annamHelper,
        array $data = []
    )
    {
        $this->logger = $logger;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
        $this->eavConfig = $eavConfig;
        $this->dishRepository = $dishRepository;
        $this->annamHelper = $annamHelper;
        $this->mealRepository = $mealRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return int
     */
    public function getParamId(): int
    {
        return (int) $this->annamHelper->getParameterValue('id');
    }

    /**
     * @return int
     */
    public function getParamDish(): int
    {
        return (int) $this->annamHelper->getParameterValue('dish');
    }

    /**
     * @return mixed
     */
    public function getMeal()
    {
        return $this->mealRepository->getById($this->getParamId());
    }

    /**
     * @return array
     */
    public function lstOptions(): array
    {
        $result = [];
        try{
            $attributeCode = $this->annamHelper->mealPlanAttribute();
            $id = $this->getParamDish();
            $dish = $this->dishRepository->getById($id);
            $data = $dish->getData();

            $listSku = [];
            if(!is_null($data['products']))
            {
                foreach (explode("," ,$data['products']) as $product)
                {
                    $listSku[] = trim($product);
                }
            }

            $searchCriteria = $this->searchCriteriaBuilder->addFilter('sku' , $listSku, 'in')->create();
            $products = $this->productRepository->getList($searchCriteria)->getItems();

            $array = [];
            foreach ($products as $product) {
                $ingredient = $product->getData($attributeCode);
                if ($ingredient) {
                    $array = array_merge($array, explode(',', $ingredient));
                }
            }

            $lstValueAttribute = array_values(array_filter(array_unique($array)));
            $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
            foreach ($lstValueAttribute as $value)
            {
                $result[] = [
                    'id' => $value,
                    'name' => $attribute->getSource()->getOptionText($value)
                ];
            }
        }catch (\Exception $e)
        {
            $this->logger->info($e->getMessage());
        }

        return $result;
    }
}
