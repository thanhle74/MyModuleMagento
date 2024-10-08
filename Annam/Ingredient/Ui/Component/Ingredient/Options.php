<?php
declare(strict_types=1);
namespace Annam\Ingredient\Ui\Component\Ingredient;

use Magento\Framework\Data\OptionSourceInterface;

class Options implements OptionSourceInterface
{

    /**
     * toOptionArray
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $array = [
            __('Complex Carbonydrates'),
            __('Vitamins'),
            __('Fiber'),
            __('Antioxidants'),
            __('Minerals')
        ];
        $data = [];

        foreach($array as $key => $value)
        {
            $data[] = [
                "value" => $key,
                "label" => $value,
            ];
        }

        return $data;
    }
}
