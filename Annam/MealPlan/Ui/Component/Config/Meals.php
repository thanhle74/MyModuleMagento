<?php
declare (strict_types = 1);
namespace Annam\MealPlan\Ui\Component\Config;

use Magento\Framework\Data\OptionSourceInterface;

class Meals implements OptionSourceInterface
{
    const BREAKFAST =  1;
    const LUNCH = 2;
    const SNACK = 3;
    const DINNER = 4;

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::BREAKFAST,
                'label' => __('Breakfast')
            ],
            [
                'value' => self::LUNCH,
                'label' => __('Lunch')
            ],
            [
                'value' => self::SNACK,
                'label' => __('Snack')
            ],
            [
                'value' => self::DINNER,
                'label' => __('Dinner')
            ],
        ];
    }
}
