<?php
declare(strict_types=1);
namespace Annam\Dish\Model;

use Annam\Dish\Model\ResourceModel\Dish\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Serialize\SerializerInterface;

class DishDataProvider extends AbstractDataProvider
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
        CollectionFactory $dishCollectionFactory,
        StoreManagerInterface $storeManager,
        SerializerInterface $serializer,
        array $meta = [],
        array $data = []
    )
    {
        $this->storeManager = $storeManager;
        $this->collection = $dishCollectionFactory->create();
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
            $data = $item->getData();

            $data['short_content'] = isset($data['short_content']) ? $this->serializer->unserialize($data['short_content']) : null;
            $data['long_content'] = isset($data['long_content']) ? $this->serializer->unserialize($data['long_content']) : null;
            $data['header_logo_src'] = isset($data['image']) ? array_values($this->serializer->unserialize($data['image'])) : null;
            $data['video'] = $data['video'] ?? null;
            $data['products'] = isset( $data['products']) ? explode(',', $data['products']) :   null;

            $this->loadedData[$item->getId()] = $data;
        }

        return $this->loadedData;
    }
}
