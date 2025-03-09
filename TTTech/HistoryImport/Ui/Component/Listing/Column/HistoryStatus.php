<?php
declare(strict_types=1);
namespace TTTech\HistoryImport\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class HistoryStatus extends Column
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
                if (isset($item['status']))
                {
                    $item['status'] = $item['status'] == 'Success'
                        ? '<span class="grid-severity-notice">' . __('Success') . '</span>'
                        : '<span class="grid-severity-minor">' . __('Error') . '</span>';
                }
            }
        }

        return $dataSource;
    }
}
