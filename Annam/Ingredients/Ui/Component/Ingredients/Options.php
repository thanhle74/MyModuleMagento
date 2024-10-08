<?php
declare (strict_types = 1);
namespace Annam\Ingredients\Ui\Component\Ingredients;

use Magento\Framework\Data\OptionSourceInterface;
use Annam\Ingredient\Model\ResourceModel\Ingredient\CollectionFactory;

class Options implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct
    (
        CollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('status', ['eq' => 1]);
        $array = $collection->getData();
        $data = [];
        if(count($array))
        {
            foreach ($array as $item)
            {
                $data[] = [
                    "value" => (int) $item['id'],
                    "label" => $item['name'],
                ];
            }
        }

        return $data;
    }
}
