<?php
declare(strict_types=1);
namespace TTTech\Import\Service;

use Exception;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;

class CreateCategoryService
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected CategoryRepositoryInterface $categoryRepository;

    /**
     * @var CategoryFactory
     */
    protected CategoryFactory $categoryFactory;

    /**
     * @var CategoryResource
     */
    protected CategoryResource $categoryResource;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var CategoryCollectionFactory
     */
    protected CategoryCollectionFactory $categoryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryFactory $categoryFactory
     * @param CategoryResource $categoryResource
     * @param LoggerInterface $logger
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        CategoryFactory $categoryFactory,
        CategoryResource $categoryResource,
        LoggerInterface $logger,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        $this->categoryResource = $categoryResource;
        $this->logger = $logger;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $categoryName
     * @return int
     * @throws Exception
     */
    public function createCategoryIfNotExists(string $categoryName)
    {
        try {
            $categoryId = $this->getCategoryIdByName($categoryName);
            if ($categoryId) {
                $this->logger->info("Category '{$categoryName}' already exists with ID: " . $categoryId);
                return $categoryId;
            }

            $parentId = $this->storeManager->getStore()->getRootCategoryId();
            $parentCategory = $this->categoryFactory->create()->load($parentId);

            $category = $this->categoryFactory->create();
            $category->setPath($parentCategory->getPath());
            $category->setParentId($parentId);
            $category->setName($categoryName);
            $category->setIsActive(false);
            $category->setUrlKey(strtolower(str_replace(' ', '-', $categoryName)));
            $category->save();

            return (int)$category->getId();
        } catch (LocalizedException $e) {
            $this->logger->error("Error creating category '{$categoryName}': " . $e->getMessage());
            return null;
        }
    }

    /**
     *
     * @param string $categoryName
     * @return int|null
     * @throws LocalizedException
     */
    private function getCategoryIdByName(string $categoryName): ?int
    {
        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addAttributeToFilter('name', $categoryName);

        $category = $categoryCollection->getFirstItem();
        if ($category && $category->getId()) {
            return (int)$category->getId();
        }

        return null;
    }
}
