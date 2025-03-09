<?php
declare(strict_types=1);

namespace Forix\PageBuilderToggle\Plugin;

use Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider;

class ProductForm
{
    /**
     * @param ProductDataProvider $subject
     * @param $meta
     * @return mixed
     */
    public function afterGetMeta(ProductDataProvider $subject, $meta)
    {
        if (isset($meta['content']['children']['container_description'])) {
            unset($meta['content']['children']['container_description']);
        }

        return $meta;
    }
}
