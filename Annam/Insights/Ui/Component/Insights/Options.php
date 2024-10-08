<?php
declare (strict_types = 1);
namespace Annam\Insights\Ui\Component\Insights;

use Magento\Framework\Data\OptionSourceInterface;
use Annam\Ingredients\Model\ResourceModel\Ingredients\CollectionFactory;

class Options implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collection;

    /**
     * @param CollectionFactory $collection
     */
    public function __construct
    (
        CollectionFactory $collection
    )
    {
        $this->collection = $collection;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $collection = $this->collection->create();
        $collection->addFieldToFilter('status', ['eq' => 1]);

        $listIngredients = $collection->getData();
        $data = [];
        if(count($listIngredients))
        {
            foreach ($listIngredients as $ingredients)
            {
                $data[] = [
                    "value" => (int) $ingredients['id'],
                    "label" => $ingredients['name'],
                ];
            }
        }

        return $data;
    }
}
