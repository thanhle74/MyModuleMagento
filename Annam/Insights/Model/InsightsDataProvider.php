<?php
declare(strict_types=1);
namespace Annam\Insights\Model;

use Annam\Insights\Model\ResourceModel\Insights\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Serialize\SerializerInterface;

class InsightsDataProvider extends AbstractDataProvider
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

    public function __construct
    (
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        SerializerInterface $serializer,
        array $meta = [],
        array $data = []
    )
    {
        $this->storeManager = $storeManager;
        $this->collection = $collectionFactory->create();
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

            $data['store'] = isset($data['store']) ? $this->serializer->unserialize($data['store']) : null;
            $data['infographic_ingredients'] = isset($data['infographic_ingredients']) ? $this->serializer->unserialize($data['infographic_ingredients']) : null;
            $data['definition'] = isset($data['definition']) ? $this->serializer->unserialize($data['definition']) : null;
            $data['benefits'] = isset($data['benefits']) ? $this->serializer->unserialize($data['benefits']) : null;
            $data['header_logo_src'] = isset($data['image']) ? $this->serializer->unserialize($data['image']) : null;

            $this->loadedData[$item->getId()] = $data;
        }
        return $this->loadedData;
    }
}
