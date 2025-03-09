<?php
declare(strict_types=1);

namespace Forix\PageBuilderToggle\Plugin;

use Magento\Catalog\Model\Category\DataProvider;
use Magento\Framework\Exception\NoSuchEntityException;

class CategoryDataProviderPlugin
{
    /**
     * @param DataProvider $subject
     * @param $result
     * @throws NoSuchEntityException
     */
    public function afterGetData(DataProvider $subject, $result)
    {
        $category = $subject->getCurrentCategory();

        if (!$category || !$category->getId()) {
            return $result;
        }

        $categoryId = (int) $category->getId();

        if(!isset($result[$categoryId]['use_page_builder'])) {
            $result[$categoryId]['use_page_builder'] = 0;
        }

        if (!isset($result[$categoryId]) || !isset($result[$categoryId]['description'])) {
            return $result;
        }

        $result[$categoryId]['pagebuilder_content'] = $result[$categoryId]['description'];

        return $result;
    }
}
