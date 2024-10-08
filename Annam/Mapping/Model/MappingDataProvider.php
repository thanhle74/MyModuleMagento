<?php
declare(strict_types=1);
namespace Annam\Mapping\Model;

use Annam\Mapping\Model\ResourceModel\Mapping\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Serialize\SerializerInterface;

class MappingDataProvider extends AbstractDataProvider
{

    protected $loadedData;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $ingredientCollectionFactory,
        StoreManagerInterface $storeManager,
        SerializerInterface $serializer,
        array $meta = [],
        array $data = []
    )
    {
        $this->storeManager = $storeManager;
        $this->collection = $ingredientCollectionFactory->create();
        $this->serializer = $serializer;
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
            $this->loadedData[$item->getId()] = $item->getData();
        }
        return $this->loadedData;
    }
}
