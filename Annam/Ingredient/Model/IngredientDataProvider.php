<?php
declare(strict_types=1);
namespace Annam\Ingredient\Model;

use Annam\Ingredient\Model\ResourceModel\Ingredient\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Serialize\SerializerInterface;

class IngredientDataProvider extends AbstractDataProvider
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
            $data = $item->getData();

            $data['store_id'] = isset($data['store']) ? $this->serializer->unserialize($data['store']) : null;
            $data['short_content'] = isset($data['short_content']) ? $this->serializer->unserialize($data['short_content']) : null;
            $data['header_logo_src'] = isset($data['image']) ? $this->serializer->unserialize($data['image']) : null;
            $data['nutritional_ingredients'] = isset($data['nutritional_ingredients']) ? $this->serializer->unserialize($data['nutritional_ingredients']) : null;
            $data['products'] = isset($data['skus']) ? $this->serializer->unserialize($data['skus']) : null;

            $this->loadedData[$item->getId()] = $data;
        }
        return $this->loadedData;
    }
}
