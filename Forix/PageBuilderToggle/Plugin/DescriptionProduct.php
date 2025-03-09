<?php
declare(strict_types=1);

namespace Forix\PageBuilderToggle\Plugin;

use Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider;

class DescriptionProduct
{
    /**
     *
     * @param ProductDataProvider $subject
     * @param $result
     */
    public function afterGetData(ProductDataProvider $subject, $result)
    {
        foreach ($result as $id => $data) {
            if (isset($data['product'], $data['product']['description'])) {
                $result[$id]['product']['container_description'] = $data['product']['description'];
                $result[$id]['product']['pagebuilder_content'] = $data['product']['description'];
            }

            if(isset($data['product']['use_page_builder'])) {
                $result[$id]['product']['use_page_builder'] = $data['product']['use_page_builder'];
            }else {
                $result[$id]['product']['use_page_builder'] = 0;
            }
        }

        return $result;
    }
}
