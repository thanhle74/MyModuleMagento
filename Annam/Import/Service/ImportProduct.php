<?php
declare(strict_types=1);
namespace Annam\Import\Service;

use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollection;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Annam\HealthLab\Helper\Data as AnnamHelper;

class ImportProduct
{
    /**
     * @var ProductFactory
     */
    protected ProductFactory $productFactory;

    /**
     * @var SetFactory
     */
    protected SetFactory $setFactory;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var CategoryCollection
     */
    protected CategoryCollection $categoryCollection;

    /**
     * @var CategoryFactory
     */
    protected CategoryFactory $categoryFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var StockRegistryInterface
     */
    protected StockRegistryInterface $stockRegistry;

    /**
     * @var AttributeOptionManagementInterface
     */
    protected AttributeOptionManagementInterface $attributeOptionManagement;

    /**
     * @var AnnamHelper
     */
    protected AnnamHelper $annamHelper;

    /**
     * @param ProductFactory $productFactory
     * @param SetFactory $setFactory
     * @param StoreManagerInterface $storeManager
     * @param CategoryCollection $categoryCollection
     * @param CategoryFactory $categoryFactory
     * @param ProductRepositoryInterface $productRepository
     * @param StockRegistryInterface $stockRegistry
     * @param AttributeOptionManagementInterface $attributeOptionManagement
     * @param AnnamHelper $annamHelper
     */
    public function __construct
    (
        ProductFactory $productFactory,
        SetFactory $setFactory,
        StoreManagerInterface $storeManager,
        CategoryCollection $categoryCollection,
        CategoryFactory $categoryFactory,
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry,
        AttributeOptionManagementInterface $attributeOptionManagement,
        AnnamHelper $annamHelper
    )
    {
        $this->productFactory = $productFactory;
        $this->setFactory = $setFactory;
        $this->storeManager = $storeManager;
        $this->categoryCollection = $categoryCollection;
        $this->categoryFactory = $categoryFactory;
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->annamHelper = $annamHelper;
    }

    /**
     * @param array $importData
     * @return void
     * @throws LocalizedException
     */
    public function handle(array $importData)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/healthlab_import_product.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        if(count($importData))
        {
            foreach ($importData as $data)
            {
                $product = $this->productFactory->create();

                $sku                    =   trim($data[0]);
                $nameEN                 =   trim($data[1]);
                $nameVN                 =   trim($data[2]);
                $category               =   trim($data[3]);
                $price                  =   trim($data[4]);
                $qty                    =   trim($data[5]);
                $attribute_set          =   trim($data[6]);
                $weight                 =   trim($data[7]);
                $search_by_keyword      =   trim($data[8]);
                $healthlab_brand        =   trim($data[9]);
                $meal_plan              =   trim($data[10]);
                $short_des_en_1         =   trim($data[11]);
                $short_des_en_2         =   trim($data[12]);
                $short_des_en_3         =   trim($data[13]);
                $short_des_en_4         =   trim($data[14]);
                $short_des_vn_1         =   trim($data[15]);
                $short_des_vn_2         =   trim($data[16]);
                $short_des_vn_3         =   trim($data[17]);
                $short_des_vn_4         =   trim($data[18]);
                $detail_en              =   trim($data[19]);
                $ingredients_en         =   trim($data[20]);
                $intructions_en         =   trim($data[21]);
                $storage_en             =   trim($data[22]);
                $detail_vi              =   trim($data[23]);
                $ingredients_vi         =   trim($data[24]);
                $intructions_vi         =   trim($data[25]);
                $storage_vi             =   trim($data[26]);

                $url_key = str_replace('-',' ',$nameEN) .'-'. $sku;

                //Attribute Set Id
                $attributeSet = $this->setFactory->create();
                $attributeSetId = $attributeSet->load($attribute_set, 'attribute_set_name')->getAttributeSetId();

                $storeCodesVN = ['hcm-ap_vi','hcm-hbt_vi','hcm-taka_vi','hcm-pmh_vi','hn-xd_vi','hcm-est_vi','hcm-sgp_vi'];

                $shortDescriptionEN = "<ul><li>$short_des_en_1</li><li>$short_des_en_2</li><li>$short_des_en_3</li><li>$short_des_en_4</li></ul>";
                $shortDescriptionVN = "<ul><li>$short_des_vn_1</li><li>$short_des_vn_2</li><li>$short_des_vn_3</li><li>$short_des_vn_4</li></ul>";
                $descriptionEN = "<h4>about the product:</h4><p>$detail_en</p><h5>Ingredients:</h5><p>$ingredients_en</p><h5>instructions for use:</h5><p>$intructions_en</p><h5>storage instructions:</h5><p>$storage_en</p>";
                $descriptionVN = "<h4>Chi tiết</h4><p>$detail_vi</p><h5>Thành phần</h5><p>$ingredients_vi</p><h5>HƯỚNG DẪN SỬ DỤNG</h5><p>$intructions_vi</p><h5>HƯỚNG DẪN BẢO QUẢN</h5><p>$storage_vi</p>";

                $websites = $this->storeManager->getWebsites();
                $websiteIds = [];
                foreach ($websites as $website) {
                    $websiteIds[] = $website->getId();
                }

                /** CATEGORY */
                $listPathCategory = explode(',',$category);
                $arrayCategoryIds = [];
                foreach($listPathCategory as $pathCategory)
                {
                    $path = explode('/',$pathCategory);
                    $level = count($path) + 1;

                    $categoryName = trim(end($path));
                    $categoryCollection = $this->categoryCollection->create();
                    $categoryCollection->addAttributeToFilter('name', $categoryName);

                    foreach($categoryCollection as $collection)
                    {
                        $categoryId = $collection->getId();
                        $categoryByFactory = $this->categoryFactory->create()->load($categoryId);
                        if($categoryByFactory->getLevel() == $level)
                        {
                            $arrayCategoryIds[] = $categoryId;
                        }
                    }
                }
                /** END CATEGORY */

                try {
                    $product->setTypeId('simple')
                        ->setStatus(1)
                        ->setAttributeSetId($attributeSetId)
                        ->setName($nameEN)
                        ->setSku($sku)
                        ->setWeight($weight)
                        ->setPrice($price)
                        ->setTaxClassId(0) // 0 = None
                        ->setCategoryIds($arrayCategoryIds)
                        ->setDescription($descriptionEN)
                        ->setShortDescription($shortDescriptionEN)
                        ->setUrlKey($url_key)
                        ->setWebsiteIds($websiteIds)
                        ->setStoreId(0)
                        ->setVisibility(4)
                        ->save();

                    foreach($storeCodesVN as $storeCode)
                    {
                        $storeId = $this->storeManager->getStore($storeCode)->getId();
                        $product->loadByAttribute('sku', $sku);
                        $product->setName($nameVN);
                        $product->setShortDescription($shortDescriptionVN);
                        $product->setDescription($descriptionVN);
                        $product->setStoreId($storeId);
                        $product->save();
                    }

                    //Set Attribute
                    $product = $this->productRepository->get($sku);
                    $product->setData(
                        $this->annamHelper->searchByKeyword(),
                        $this->getIdOptions(
                            $this->annamHelper->searchByKeyword(),
                            explode(',',$search_by_keyword)
                        )
                    );
                    $product->setData(
                        $this->annamHelper->brand(),
                        $this->getIdOptions(
                            $this->annamHelper->brand(),
                            explode(',',$healthlab_brand)
                        )
                    );
                    $product->setData(
                        $this->annamHelper->mealPlanAttribute(),
                        $this->getIdOptions(
                            $this->annamHelper->mealPlanAttribute(),
                            explode(',',$meal_plan)
                        )
                    );

                    $product->setData($this->annamHelper->getAttributeInfographic(), 1);
                    $product->setStoreId(0);
                    $this->productRepository->save($product);

                    echo "Import Product Done SKU: ".$sku . "\n";

                } catch (\Exception $e) {
                    $logger->info('Error importing product sku: ' . $sku . '. ' . $e->getMessage());
                    continue;
                }

                try {
                    $product = $this->productRepository->get($sku);
                    $productId = $product->getId();

                    $stockItem = $this->stockRegistry->getStockItem($productId);
                    $stockItem->setQty($qty);
                    $this->stockRegistry->updateStockItemBySku($sku, $stockItem);

                } catch (\Exception $e) {
                    $logger->info('Error importing stock for product sku: ' . $sku . '. ' . $e->getMessage());
                    continue;
                }
                unset($product);
            }
        }
    }

    /**
     * @param string $attributeCode
     * @param $optionNames
     * @return array
     * @throws InputException
     * @throws StateException
     */
    private function getIdOptions(string $attributeCode, $optionNames): array
    {
        $attributeOption = $this->attributeOptionManagement->getItems('catalog_product', $attributeCode);
        $optionIds = [];
        foreach($optionNames as $optionName)
        {
            foreach ($attributeOption as $option) {
                if ($option->getLabel() == $optionName) {
                    $optionIds[] = $option->getValue();
                }
            }
        }

        return $optionIds;
    }
}
