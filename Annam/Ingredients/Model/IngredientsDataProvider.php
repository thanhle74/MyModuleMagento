<?php
declare(strict_types=1);
namespace Annam\Ingredients\Model;

use Annam\Ingredients\Model\ResourceModel\Ingredients\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Serialize\SerializerInterface;

class IngredientsDataProvider extends AbstractDataProvider
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
        CollectionFactory $ingredientsCollectionFactory,
        StoreManagerInterface $storeManager,
        SerializerInterface $serializer,
        array $meta = [],
        array $data = []
    )
    {
        $this->storeManager = $storeManager;
        $this->collection = $ingredientsCollectionFactory->create();
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
            $data['short_content'] = isset($data['short_content']) ? $this->serializer->unserialize($data['short_content']) : null;
            $data['ingredients'] = isset($data['ingredients']) ? $this->serializer->unserialize($data['ingredients']) : null;
            $data['header_logo_src'] = isset($data['image']) ? $this->serializer->unserialize($data['image']) : null;
            $data['head_shortcut_icon'] = isset($data['banner']) ? $this->serializer->unserialize($data['banner']) : null;

            $this->loadedData[$item->getId()] = $data;
        }
        return $this->loadedData;
    }
}
