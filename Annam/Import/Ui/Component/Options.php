<?php
declare (strict_types = 1);
namespace Annam\Import\Ui\Component;

use Magento\Framework\Data\OptionSourceInterface;
use Annam\Import\Ui\Component\HealthlabImportInterface;

class Options implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                "value" => HealthlabImportInterface::MAPPING,
                "label" => __('Mapping'),
            ],
            [
                "value" => HealthlabImportInterface::PRODUCTS,
                "label" => __('Products'),
            ],
            [
                "value" => HealthlabImportInterface::DISH,
                "label" => __('Grid -- Dish'),
            ],
            [
                "value" => HealthlabImportInterface::GRID_MEAL_PLAN,
                "label" => __('Grid -- Meal Plan'),
            ],
            [
                "value" => HealthlabImportInterface::INGREDIENT,
                "label" => __('Grid -- Ingredient'),
            ],
            [
                "value" => HealthlabImportInterface::INGREDIENTS,
                "label" => __('Grid -- Ingredients'),
            ],
            [
                "value" => HealthlabImportInterface::SEARCH_BY_KEYWORD,
                "label" => __('Attribute -- Search By Keyword'),
            ],
            [
                "value" => HealthlabImportInterface::MEAL_PLAN,
                "label" => __('Attribute -- Meal Plan'),
            ],
            [
                "value" => HealthlabImportInterface::HEALTHLAB_BRAND,
                "label" => __('Attribute -- Healthlab Brand'),
            ],
        ];
    }
}
