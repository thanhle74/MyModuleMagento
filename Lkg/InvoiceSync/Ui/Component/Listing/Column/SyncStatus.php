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

class SyncStatus extends Column
{
    /**
     * Prepare data source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                // Adjust the value based on your source model or logic
                if ($item['sync_status'] == '1') {
                    $item['sync_status'] = __('Success');
                } elseif ($item['sync_status'] == '2') {
                    $item['sync_status'] = __('Failed');
                } elseif ($item['sync_status'] == '3') {
                    $item['sync_status'] = __('Expired');
                } else {
                    $item['sync_status'] = __('Awaiting For Approve');
                }
            }
        }

        return $dataSource;
    }
}
