<?php
declare(strict_types=1);

namespace Forix\PageBuilderToggle\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\LocalizedException;

class ModifyCategoryBeforeSave implements ObserverInterface
{
    /**
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        /** @var Category $category */
        $category = $observer->getEvent()->getCategory();
        $categoryData = $observer->getEvent()->getRequest()->getParams();

        if (!$category instanceof Category) {
            return;
        }

        if ($categoryData['pagebuilder_content'] && (int)$categoryData['use_page_builder']) {
            $category->setData('description', $categoryData['pagebuilder_content']);
        }
    }
}
