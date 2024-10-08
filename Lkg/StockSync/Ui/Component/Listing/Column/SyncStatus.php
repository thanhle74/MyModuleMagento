<?php
declare(strict_types=1);
namespace Lkg\StockSync\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Lkg\Base\Api\SyncStatusInterface;

class SyncStatus extends Column
{
    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item)
            {
                if (isset($item['sync_status']))
                {
                    $item['sync_status'] = $item['sync_status'] == SyncStatusInterface::SUCCESS
                        ? '<span class="grid-severity-notice">' . __('Success') . '</span>'
                        : '<span class="grid-severity-critical">' . __('Fail') . '</span>';
                }
            }
        }

        return $dataSource;
    }
}
