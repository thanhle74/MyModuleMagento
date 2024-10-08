<?php
declare(strict_types=1);
namespace Lkg\Base\Model;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Lkg\Base\Model\ResourceModel\CronHistory\CollectionFactory;

class CronHistoryDataProvider extends AbstractDataProvider
{
    protected $loadedData;
    protected $collectionFactory;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create(); // Initialize the collection
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();

        foreach ($items as $item) {
            $data = $item->getData();
            $this->loadedData[$item->getId()] = $data;
        }
        return $this->loadedData;
    }
}
