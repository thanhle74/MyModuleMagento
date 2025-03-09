<?php
declare(strict_types= 1);
namespace TTTech\HistoryImport\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Message extends Column
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
                if (isset($item['message'])) {
                    $item['message'] = '<textarea rows="4" readonly>' . $item['message'] . '</textarea>';
                }
            }
        }

        return $dataSource;
    }
}
