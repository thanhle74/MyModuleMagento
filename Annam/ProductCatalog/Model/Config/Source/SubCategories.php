<?php
declare (strict_types = 1);
namespace Annam\ProductCatalog\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Annam\HealthLab\Helper\Data as AnnamHelper;

class SubCategories implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $categoryCollectionFactory;

    /**
     * @var AnnamHelper
     */
    protected AnnamHelper $annamHelper;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @param CollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param AnnamHelper $annamHelper
     */
    public function __construct(
        CollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        AnnamHelper $annamHelper
    )
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->annamHelper = $annamHelper;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function toOptionArray(): array
    {
        $options = [];
        if(!empty($this->annamHelper->healthlabCategory()))
        {
            $collection = $this->getSubcategories((int) $this->annamHelper->healthlabCategory());
            foreach ($collection as $category) {
                $options[] = [
                    'value' => $category->getId(),
                    'label' => $category->getName(),
                ];
            }
        }

        return $options;
    }

    /**
     * @param int $parentId
     * @return Collection
     * @throws LocalizedException
     */
    private function getSubcategories(int $parentId): Collection
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        //$collection->addFieldToFilter('level', 2);
        $collection->addFieldToFilter('parent_id', $parentId);
        $collection->addIsActiveFilter();

        return $collection;
    }
}
