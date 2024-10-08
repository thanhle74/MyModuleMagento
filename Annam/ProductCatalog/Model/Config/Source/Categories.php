<?php
declare (strict_types = 1);
namespace Annam\ProductCatalog\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

class Categories implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $categoryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @param CollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager
    )
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function toOptionArray(): array
    {
        $store = $this->storeManager->getStore();
        $rootCategoryId = $store->getRootCategoryId();

        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter('level', 2);
        $collection->addFieldToFilter('parent_id', $rootCategoryId);

        $options = [];

        foreach ($collection as $category) {
            $options[] = [
                'value' => $category->getId(),
                'label' => $category->getName(),
            ];
        }

        return $options;
    }
}
