<?php
declare(strict_types=1);
namespace TTTech\Import\Service;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use TTTech\Import\Logger\Logger;
use Exception;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use TTTech\Import\Service\AssignAttributesToGroup;
use TTTech\Import\Service\OptionAttributeService;
use Magento\Catalog\Model\Product\Visibility;
use TTTech\HistoryImport\Model\HistoryImportFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use TTTech\Import\Helper\Data as ImportHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogInventory\Model\Stock\ItemFactory;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Item as StockItem;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use TTTech\Import\Model\ImportProduct;
use TTTech\Import\Api\AttributeInterface;

class CreateSimpleProduct
{
    /**
     * @var ProductFactory
     */
    protected ProductFactory $productFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var Logger
     */
    protected Logger $logger;

    /**
     * @var \TTTech\Import\Service\AssignAttributesToGroup
     */
    protected AssignAttributesToGroup $assignAttributesToGroup;

    /**
     * @var \TTTech\Import\Service\OptionAttributeService
     */
    protected OptionAttributeService $optionAttributeService;

    /**
     * @var HistoryImportFactory
     */
    protected HistoryImportFactory $historyImportFactory;

    /**
     * @var ImportHelper
     */
    protected ImportHelper $importHelper;

    /**
     * Summary of apiService
     * @var ApiService
     */
    protected ApiService $apiService;

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
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \TTTech\Import\Logger\Logger $logger
     * @param \TTTech\Import\Service\AssignAttributesToGroup $assignAttributesToGroup
     * @param \TTTech\Import\Service\OptionAttributeService $optionAttributeService
     * @param \TTTech\HistoryImport\Model\HistoryImportFactory $historyImportFactory
     * @param \TTTech\Import\Helper\Data $importHelper
     * @param \TTTech\Import\Service\ApiService $apiService
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\CatalogInventory\Model\Stock\ItemFactory $itemFactory
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock\Item $stockItem
     * @param \Magento\Framework\Filesystem $fileSystem
     */
    public function __construct(
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        Logger $logger,
        AssignAttributesToGroup $assignAttributesToGroup,
        OptionAttributeService $optionAttributeService,
        HistoryImportFactory $historyImportFactory,
        ImportHelper $importHelper,
        ApiService $apiService,
        StoreManagerInterface   $storeManagerInterface,
        ItemFactory $itemFactory,
        StockItem $stockItem,
        Filesystem $fileSystem
    ) {
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->assignAttributesToGroup = $assignAttributesToGroup;
        $this->optionAttributeService = $optionAttributeService;
        $this->historyImportFactory = $historyImportFactory;
        $this->importHelper = $importHelper;
        $this->apiService = $apiService;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->itemFactory = $itemFactory;
        $this->stockItem = $stockItem;
        $this->fileSystem = $fileSystem;
    }

    /**
     * Summary of create
     * @param array $variant
     * @param array $productData
     * @param int $optionIdColor
     * @param string $imagePath
     * @param bool $isUpdate
     * @param mixed $output
     * @param array $categoryId
     * @return int|null
     */
    public function create(
        array $variant,
        array $productData,
        int $optionIdColor,
        $brand,
        $modelNo,
        string $imagePath = '',
        bool $isUpdate = false,
        $output = null,
        array $categoryId = []
    )
    {
        // $this->assignAttributesToGroup->execute(
        //     AttributeInterface::ATTRIBUTE_SET_NAME,
        //     AttributeInterface::ATTRIBUTE_GROUP_NAME,
        //     [AttributeInterface::ATTRIBUTE_SIZE]
        // );
        $optionId = $this->optionAttributeService->createOrGetId(AttributeInterface::ATTRIBUTE_SIZE, $variant['size']);

        $qty = 0 ;
        $endpoint = 'webshop/' . $this->importHelper->getWebshop() .'/stock';
        $stocksApiData = $this->apiService->getApiData($endpoint);
        if(isset($stocksApiData['data'])){
            $stocksApiData = $stocksApiData['data'];
            foreach ($stocksApiData as $stock)
            {
                if($stock['variantId'] == $variant['id'])
                {
                    $qty = (int)$stock['stock'];
                }
            }    
        }
        
        $sku = $variant['ean'];
        $price = $variant['price'];
        $salePrice = $variant['salePrice'];
        $nameProduct = $productData['description1'];
        $variant['sku'] = $sku;
        $weight = 1;
        $url_key = $nameProduct .'-'. $productData['id'];
        $url_key = str_replace('-', ' ', $url_key);

        try {
            $simpleProduct = $this->productFactory->create();
            $productId = $variant['productId'];
            if($isUpdate)
            {
                $simpleProduct = $this->productRepository->get($sku);
            }else{
                $simpleProduct->setSku($sku);
            }

            $websites = $this->storeManagerInterface->getWebsites();
            $websiteIds = [];
            foreach ($websites as $website) {
                $websiteIds[] = $website->getId();
            }

            $simpleProduct->setStoreId(0); // Default store view
            $simpleProduct->setName($nameProduct);
            $simpleProduct->setAttributeSetId(4);
            $simpleProduct->setCategoryIds($categoryId);
            $simpleProduct->setStatus(Status::STATUS_ENABLED);
            $simpleProduct->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE);
            $simpleProduct->setTypeId(ProductType::TYPE_SIMPLE);
            $simpleProduct->setPrice($price);
            $simpleProduct->setSpecialPrice($salePrice);
            $simpleProduct->setData('product_id', $productId);
            $simpleProduct->setWeight($weight);
            //$simpleProduct->setIsInStock(1);
            $simpleProduct->setWebsiteIds($websiteIds);
            $simpleProduct->setUrlKey($url_key);
            $simpleProduct->setStockData(
                [
                    'use_config_manage_stock' => 1,
                    'qty' => $qty,
                    'is_in_stock' => $qty ? 1 : 0,
                ]
            );

            /**
             * Set attribute
             */
            $simpleProduct->setBrand($brand);
            $simpleProduct->setModelNo($modelNo);
            $simpleProduct->setCustomAttribute(AttributeInterface::ATTRIBUTE_COLOR, $optionIdColor);
            $simpleProduct->setCustomAttribute(AttributeInterface::ATTRIBUTE_SIZE, (int)$optionId);

            /**
             * Add Images To The Product
             */
            if(trim($imagePath)){
                if (file_exists($imagePath) && getimagesize($imagePath)){

                    //Remove Image
                    $existingMediaGalleryEntries = $simpleProduct->getMediaGalleryEntries();
                    if(is_array($existingMediaGalleryEntries)){
                        if(count($existingMediaGalleryEntries) > 0) {
                            foreach ($existingMediaGalleryEntries as $key => $entry) {
                                unset($existingMediaGalleryEntries[$key]);
                            }
                            
                            $simpleProduct->setMediaGalleryEntries($existingMediaGalleryEntries);
                            $this->productRepository->save($simpleProduct);
                        }
                    }

                    $simpleProduct->addImageToMediaGallery($imagePath, ['image', 'small_image', 'thumbnail'], false, false);
                }
            }

            $simpleProduct->save();

            /**
             * Stock
             * 
            */
            $stockModel = $this->itemFactory->create();
            $this->stockItem->load($stockModel, $simpleProduct->getId(),"product_id");
            $stockModel->setQty($qty);
            $this->stockItem->save($stockModel);

            if($isUpdate) {
                if($output){
                    $output->writeln("Update Done Simple Product with SKU: ".$sku);
                }

                $this->log(
                    $variant,
                    'Success',
                    'Simple product' ,
                    'Successfully updated simple product with SKU: '.$sku
                );
            }else{
                if($output){
                    $output->writeln("Create Done Simple Product with SKU: ".$sku);
                }

                $this->log(
                    $variant,
                    'Success',
                    'Simple product' , 
                    'Successfully created simple product with SKU: '.$sku
                );
            }

            return (int)$this->productRepository->get($sku)->getId();
        } catch (Exception $e)
        {
            $this->logger->error('Error creating simple product: ' . $e->getMessage());

            if($isUpdate){
                if($output){
                    $output->writeln('Error Update Simple Product with SKU: '.$sku);
                }

                $this->log(
                    $variant, 
                    'Error',
                    'Simple product' ,
                    'Error updating simple product: ' . $e->getMessage()
                );
            }else {
                if($output){
                    $output->writeln('Error Create Simple Product with SKU: '.$sku);
                }

                $this->log(
                    $variant, 
                    'Error',
                    'Simple product' ,
                    'Error creating simple product: ' . $e->getMessage()
                );
            }

            return null;
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
        $logEntry = $this->historyImportFactory ->create();

        // Set log entry data
        $logEntry->setName($data['sku'] ?? 'N/A')
            ->setJobKey($data['job_key'])
            ->setType($logType)
            ->setMessage($message)
            ->setStatus($status)
            ->setDataJson(json_encode($data))
            ->setCreatedAt(date('Y-m-d H:i:s'))
            ->setUpdatedAt(date('Y-m-d H:i:s'))
            ->save();
    }
}
