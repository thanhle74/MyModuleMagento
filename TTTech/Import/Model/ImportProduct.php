<?php
declare(strict_types=1);
namespace TTTech\Import\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Validator\ValidateException;
use TTTech\Import\Service\ApiService;
use TTTech\Import\Helper\Data as ImportHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use TTTech\Import\Service\CreateSimpleProduct;
use TTTech\Import\Logger\Logger;
use Exception;
use TTTech\Import\Service\ConfigurableProductService;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use TTTech\Import\Service\AssignAttributesToGroup;
use TTTech\Import\Service\OptionAttributeService;
use TTTech\Import\Service\CreateSizeAttribute;
use TTTech\Import\Service\CreateColorAttribute;
use TTTech\Import\Service\CreateCategoryService;
use TTTech\HistoryImport\Model\HistoryImportFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogInventory\Model\Stock\ItemFactory;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Item as StockItem;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use TTTech\Import\Api\AttributeInterface;

class ImportProduct
{
    /**
     * Summary of apiService
     * @var ApiService
     */
    protected ApiService $apiService;

    /**
     * @var ImportHelper
     */
    protected ImportHelper $importHelper;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var ProductFactory
     */
    protected ProductFactory $productFactory;

    /**
     * @var Logger
     */
    protected Logger $logger;

    /**
     * @var CreateSimpleProduct
     */
    protected CreateSimpleProduct $createSimpleProduct;

    /**
     * @var ConfigurableProductService
     */
    protected ConfigurableProductService $configurableProductService;

    /**
     * @var AssignAttributesToGroup
     */
    protected AssignAttributesToGroup $assignAttributesToGroup;

    /**
     * @var OptionAttributeService
     */
    protected OptionAttributeService $optionAttributeService;

    /**
     * @var CreateSizeAttribute
     */
    protected CreateSizeAttribute $createSizeAttribute;

    /**
     * @var CreateColorAttribute
     */
    protected CreateColorAttribute $createColorAttribute;

    /**
     * @var HistoryImportFactory
     */
    protected HistoryImportFactory $historyImportFactory;

    /**
     * @var CreateCategoryService
     */
    protected CreateCategoryService $createCategoryService;

    /**
     * Summary of storeManagerInterface
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManagerInterface;

    /**
     * Summary of itemFactory
     * @var ItemFactory
     */
    protected ItemFactory $itemFactory;

    /**
     * Summary of stockItem
     * @var StockItem
     */
    protected StockItem $stockItem;

    /**
     * Summary of filesystem
     * @var Filesystem
     */
    protected Filesystem $fileSystem;

    /**
     * Summary of __construct
     * @param \TTTech\Import\Service\ApiService $apiService
     * @param \TTTech\Import\Helper\Data $importHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \TTTech\Import\Logger\Logger $logger
     * @param \TTTech\Import\Service\CreateSimpleProduct $createSimpleProduct
     * @param \TTTech\Import\Service\ConfigurableProductService $configurableProductService
     * @param \TTTech\Import\Service\AssignAttributesToGroup $assignAttributesToGroup
     * @param \TTTech\Import\Service\OptionAttributeService $optionAttributeService
     * @param \TTTech\Import\Service\CreateSizeAttribute $createSizeAttribute
     * @param \TTTech\Import\Service\CreateColorAttribute $createColorAttribute
     * @param \TTTech\HistoryImport\Model\HistoryImportFactory $historyImportFactory
     * @param \TTTech\Import\Service\CreateCategoryService $createCategoryService
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\CatalogInventory\Model\Stock\ItemFactory $itemFactory
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock\Item $stockItem
     * @param \Magento\Framework\Filesystem $fileSystem
     */
    public function __construct(
        ApiService $apiService,
        ImportHelper $importHelper,
        ProductRepositoryInterface $productRepository,
        ProductFactory $productFactory,
        Logger $logger,
        CreateSimpleProduct $createSimpleProduct,
        ConfigurableProductService $configurableProductService,
        AssignAttributesToGroup $assignAttributesToGroup,
        OptionAttributeService $optionAttributeService,
        CreateSizeAttribute $createSizeAttribute,
        CreateColorAttribute $createColorAttribute,
        HistoryImportFactory $historyImportFactory,
        CreateCategoryService $createCategoryService,
        StoreManagerInterface $storeManagerInterface,
        ItemFactory $itemFactory,
        StockItem $stockItem,
        Filesystem $fileSystem

    ) {
        $this->apiService = $apiService;
        $this->importHelper = $importHelper;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->logger = $logger;
        $this->createSimpleProduct = $createSimpleProduct;
        $this->configurableProductService = $configurableProductService;
        $this->assignAttributesToGroup = $assignAttributesToGroup;
        $this->optionAttributeService = $optionAttributeService;
        $this->createSizeAttribute = $createSizeAttribute;
        $this->createColorAttribute = $createColorAttribute;
        $this->historyImportFactory = $historyImportFactory;
        $this->createCategoryService = $createCategoryService;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->itemFactory = $itemFactory;
        $this->stockItem = $stockItem;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws ValidateException
     * @throws InputException
     * @throws StateException
     * @throws Exception
     */
    public function import($output = null)
    {
        $endpoint = 'webshop/' . $this->importHelper->getWebshop() . '/product';
        $productsApiData = $this->apiService->getApiData($endpoint);
        $productsApiData = $productsApiData['data'];

        //Check isset and Create Attribute
        //$this->createSizeAttribute->create();
        //$this->createColorAttribute->create();

        if (!isset($productsApiData['products']) || !isset($productsApiData['variants'])) {
            $this->logger->error('Invalid product data from API.');
            return;
        }

        $isDeveloper = $this->importHelper->getDeveloper();
        if($isDeveloper) {
            if($this->importHelper->getLoop()){
                $i = 0;
            }
            $specificId = $this->importHelper->getSpecificId();
        };

        $jobKey = bin2hex(random_bytes(8));

        foreach ($productsApiData['products'] as $productData) {
            if($isDeveloper) {
                if($this->importHelper->getLoop()){
                    $i++;
                }else{
                   if($productData['id'] != $specificId){
                        continue;
                   }
                }
            };

            if (!isset($productData['articleNumber'], $productData['articleColor'], $productData['categoryName'], $productData['description1'])) {
                $this->logger->error('Missing essential product data.');
                continue;
            }

            $productData['job_key'] = $jobKey;
            $isProductBySku = false;
            // $this->assignAttributesToGroup->execute(
            //     AttributeInterface::ATTRIBUTE_SET_NAME,
            //     AttributeInterface::ATTRIBUTE_GROUP_NAME,
            //     [
            //         AttributeInterface::ATTRIBUTE_COLOR, 
            //         AttributeInterface::ATTRIBUTE_BRAND, 
            //         AttributeInterface::ATTRIBUTE_MODEL_NO
            //     ]
            // );
            $optionIdColor = $this->optionAttributeService->createOrGetId(AttributeInterface::ATTRIBUTE_COLOR, $productData['articleColor']);

            $skuConfigProduct = '1#' . $productData['articleNumber'] . '#' . $productData['articleColor'];
            $description = $productData['description1'];
            $nameProduct = $productData['categoryName']. ' '.$productData['articleNumber'];
            $productData['sku'] = $skuConfigProduct;
            $weight = 1;
            $qty = 0;
            $imagePath = '';
            $brand = $productData['supplierName'];
            $modelNo = $productData['articleNumber'];

            $url_key = str_replace('-', ' ', $nameProduct) . '-'.$productData['articleColor'];
            $categoryId = $this->createCategoryService->createCategoryIfNotExists($productData['categoryName']);

            $websites = $this->storeManagerInterface->getWebsites();
            $websiteIds = [];
            foreach ($websites as $website) {
                $websiteIds[] = $website->getId();
            }
            try {
                $configProduct = $this->productFactory->create();
                if ($this->isProductBySku($skuConfigProduct)) {
                    $isProductBySku = true;
                    $configProduct = $this->productRepository->get($skuConfigProduct);
                } else {
                    $configProduct->setSku($skuConfigProduct);
                }
                $configProduct->setStoreId(0);
                $configProduct->setName($nameProduct);
                $configProduct->setAttributeSetId(4);
                $configProduct->setWeight($weight);
                $configProduct->setCategoryIds([$categoryId]);
                $configProduct->setStatus(Status::STATUS_ENABLED);
                $configProduct->setVisibility(Visibility::VISIBILITY_BOTH);
                $configProduct->setTypeId(Configurable::TYPE_CODE);
                $configProduct->setDescription($description);
                $configProduct->setIsInStock(1);
                $configProduct->setUrlKey($url_key);
                $configProduct->setWebsiteIds($websiteIds);
                $configProduct->setStockData
                (
                    [
                        'use_config_manage_stock' => 1,
                        'qty' => $qty,
                        'is_in_stock' => 1
                    ]
                );

                /**
                 * Set attribute
                 */
                $configProduct->setBrand($brand);
                $configProduct->setModelNo($modelNo);
                $configProduct->setCustomAttribute(AttributeInterface::ATTRIBUTE_COLOR, (int) $optionIdColor);

                /**
                 * Add Images To The Product
                 */
                foreach ($productsApiData['images'] as $images){
                    if($images['productId'] == $productData['id']){
                        $imagePath = $images['fileName'];
                    }
                }

                if(trim($imagePath)){
                    $mediaPath = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath(AttributeInterface::ABSOLUTE_PATH);
                    $imagePath = (string)$mediaPath .'/'. $imagePath;
                    $imagePath = str_replace('\\', '/', $imagePath);
                    if (file_exists($imagePath) && getimagesize($imagePath)){

                        //Remove Image
                        $existingMediaGalleryEntries = $configProduct->getMediaGalleryEntries();
                        if(is_array($existingMediaGalleryEntries)){
                            if(count($existingMediaGalleryEntries)) {
                                foreach ($existingMediaGalleryEntries as $key => $entry) {
                                    unset($existingMediaGalleryEntries[$key]);
                                }
                                
                                $configProduct->setMediaGalleryEntries($existingMediaGalleryEntries);
                                $this->productRepository->save($configProduct);
                            }
                        }

                        $configProduct->addImageToMediaGallery($imagePath, ['image', 'small_image', 'thumbnail'], false, false);
                    }
                }

                $configProduct->save();

                /**
                 * Stock
                 */
                $stockModel = $this->itemFactory->create();
                $this->stockItem->load($stockModel, $configProduct->getId(), "product_id");
                $stockModel->setQty($qty);
                $this->stockItem->save($stockModel);

                if ($isProductBySku) {
                    if ($output) {
                        $output->writeln('Update Done Configurable Product with SKU: ' . $skuConfigProduct);
                    }

                    $this->log(
                        $productData,
                        'Success',
                        'Product Config', 
                        'Successfully updated simple product with SKU: '.$skuConfigProduct
                    );
                } else {
                    if ($output) {
                        $output->writeln('Create Done Configurable Product with SKU: ' . $skuConfigProduct);
                    }
                    $this->log(
                        $productData,
                        'Success',
                        'Product Config',
                        'Successfully created simple product with SKU: '.$skuConfigProduct
                    );
                }

            } catch (Exception $e) {
                $this->logger->error('Error creating configurable product: ' . $e->getMessage());

                if ($isProductBySku) {
                    if ($output) {
                        $output->writeln('Error update configurable product with SKU: ' . $skuConfigProduct);
                    }

                    $this->log(
                        $productData,
                        'Error',
                        'Product Config', 
                        'Error updating configurable product: ' . $e->getMessage(),
                    );
                } else {
                    if ($output) {
                        $output->writeln('Error creating configurable product with SKU: ' . $skuConfigProduct);
                    }

                    $this->log(
                        $productData,
                        'Error',
                        'Product Config', 
                        'Error creating simple product: ' . $e->getMessage()
                    );
                }
            }

            $associatedProductIds = [];
            foreach ($productsApiData['variants'] as $variant) {
                if ($variant['productId'] === $productData['id']) {
                    $variant['job_key'] = $jobKey;
                    $associatedProductIds[] = $this->createSimpleProduct->create(
                        $variant,
                        $productData,
                        (int) $optionIdColor,
                        $brand,
                        $modelNo,
                        $imagePath,
                        $this->isProductBySku($variant['ean']),
                        $output,
                        [$categoryId]
                    );
                }
            }

            if (count($associatedProductIds)) {
                $this->configurableProductService->assignAssociatedProducts($configProduct, $associatedProductIds, $skuConfigProduct);
            }

            if($isDeveloper) {
                if($this->importHelper->getLoop()){
                    if($i == $this->importHelper->getLoop())
                    {
                        break;
                    }
                }
            };
        }

        $errorCount = $this->historyImportFactory->create()->getCollection()
            ->addFieldToFilter('job_key', ['eq' => $jobKey])
            ->addFieldToFilter('status', ['eq' => 'Error'])
            ->count();

        $successCount = $this->historyImportFactory->create()->getCollection()
            ->addFieldToFilter('job_key', ['eq' => $jobKey])
            ->addFieldToFilter('status', ['eq' => 'Success'])
            ->count();
        $data = [
            'error_count' => $errorCount,
            'success_count' => $successCount,
            'job_key' => $jobKey,
            'total' => count($productsApiData['products']) + count($productsApiData['variants'])
        ];

        $this->log(
            $data,
            'Success',
            'Total', 
            'Done'
        );
    }

    /**
     * @param $sku
     * @return bool
     */
    private function isProductBySku($sku): bool
    {
        try {
            $this->productRepository->get($sku);
            return true;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @param array $data
     * @param string $status
     * @param string $logType
     * @param string $message
     * @return void
     * @throws CouldNotSaveException
     */
    protected function log(array $data, string $status, string $logType, string $message): void
    {
        $logEntry = $this->historyImportFactory->create();

        if($logType == 'Total'){
            $logEntry->setErrorCount($data['error_count']);
            $logEntry->setSuccessCount($data['success_count']);
            $logEntry->setTotal($data['total']);
        }else{
            $logEntry->setName($data['sku'] ?? 'N/A');
            $logEntry->setMessage($message);
            $logEntry->setStatus($status);
            $logEntry->setDataJson(json_encode($data));
        }

        $logEntry->setJobKey($data['job_key']);
        $logEntry->setType($logType);
        $logEntry->setCreatedAt(date('Y-m-d H:i:s'));
        $logEntry->setUpdatedAt(date('Y-m-d H:i:s'));
        $logEntry->save();
    }
}
