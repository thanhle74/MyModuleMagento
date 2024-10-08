<?php

/**
 * @author ICT
 * @copyright Copyright (c) 2024 Juni.com
 * @package A Magento 2 Invoice SYNC To LKG System Module.
 */

namespace Lkg\InvoiceSync\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Response extends Column
{
    /**
     * Constructor for the class.
     *
     * @param array $dataSource Instance for managing prepare data.
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['response'])) {
                    $item['response'] = '<textarea rows="4" readonly>' . $item['response'] . '</textarea>';
                }
            }
        }

        return $dataSource;
    }
}
