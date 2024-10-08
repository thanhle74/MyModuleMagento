<?php
declare (strict_types = 1);
namespace Annam\Dish\Ui\Component\Dish;

use Magento\Framework\Data\OptionSourceInterface;
use Annam\Dish\Model\ResourceModel\Dish\CollectionFactory;

class Options implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $dishCollection;

    /**
     * @param CollectionFactory $dishCollection
     */
    public function __construct
    (
        CollectionFactory $dishCollection
    )
    {
        $this->dishCollection = $dishCollection;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $dishCollection = $this->dishCollection->create();
        $dishCollection->addFieldToFilter('status', ['eq' => 1]);

        $listDist = $dishCollection->getData();
        $data = [];
        if(count($listDist))
        {
            foreach ($listDist as $dish)
            {
                $data[] = [
                    "value" => (int) $dish['id'],
                    "label" => $dish['name'],
                ];
            }
        }

        return $data;
    }
}
