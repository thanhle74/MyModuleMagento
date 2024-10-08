<?php
declare(strict_types=1);
namespace Annam\MealPlan\Model;

use Annam\MealPlan\Model\ResourceModel\Meal\CollectionFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class MealPlanDataProvider extends AbstractDataProvider
{
    protected $loadedData;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    public function __construct
    (
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $JobCollectionFactory,
        SerializerInterface $serializer,
        array $meta = [],
        array $data = []
    )
    {
        $this->collection = $JobCollectionFactory->create();
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

            for ($i = 1; $i <= 7; $i++) {
                $dayKey = 'day_' . $i;
                $fieldsetKey = 'fieldset_day_' . $i;
                if (isset($data[$dayKey])) {
                    $data[$fieldsetKey]['list_dish'] = $this->serializer->unserialize($data[$dayKey]);
                }
            }

            $data['content'] = isset($data['content']) ? $this->serializer->unserialize($data['content']) : null;
            $data['store_id'] = isset($data['store']) ? $this->serializer->unserialize($data['store']) : null;
            $data['header_logo_src'] = isset($data['image']) ? $this->serializer->unserialize($data['image']) : null;


            $this->loadedData[$item->getId()] = $data;
        }

        return $this->loadedData;
    }
}
