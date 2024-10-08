<?php
declare(strict_types=1);
namespace Annam\HealthLab\Block;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Annam\HealthLab\Helper\Data as AnnamHelper;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;
use Psr\Log\LoggerInterface;

abstract class AbstractCategory extends Template
{
    /**
     * @var AnnamHelper
     */
    protected AnnamHelper $annamHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    protected CategoryRepositoryInterface $categoryRepository;

    /**
     * @var CategoryCollectionFactory
     */
    protected CategoryCollectionFactory $categoryCollectionFactory;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param Context $context
     * @param AnnamHelper $annamHelper
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        AnnamHelper $annamHelper,
        CategoryCollectionFactory $categoryCollectionFactory,
        CategoryRepositoryInterface $categoryRepository,
        LoggerInterface $logger,
        array $data = []
    )
    {
        $this->annamHelper = $annamHelper;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->logger = $logger;
        parent::__construct($context, $data);
    }

    /**
     * @param int $categoryId
     * @return CategoryInterface|null
     */
    public function getCategoryById(int $categoryId): ?CategoryInterface
    {
        try {
            return $this->categoryRepository->get($categoryId);
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            return null;
        }
    }

    /**
     * @return array|Collection
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function listSubMainHealthLabCategories()
    {
        if(!empty($this->annamHelper->healthlabCategory()))
        {
            return $this->getSubcategories((int) $this->annamHelper->healthlabCategory());
        }

        return [];
    }

    /**
     * @param int $parentId
     * @return Collection
     * @throws LocalizedException
     */
    public function getSubcategories(int $parentId): Collection
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        //$collection->addFieldToFilter('level', 2);
        $collection->addFieldToFilter('parent_id', $parentId);
        $collection->addIsActiveFilter();

        return $collection;
    }
}
