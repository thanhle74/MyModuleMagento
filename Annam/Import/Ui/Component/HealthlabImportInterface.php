<?php
declare (strict_types = 1);
namespace Annam\Import\Ui\Component;

interface HealthlabImportInterface
{
    const MAPPING = 1;
    const INGREDIENT = 2;
    const INGREDIENTS = 3;
    const DISH = 4;
    const PRODUCTS = 5;
    const GRID_MEAL_PLAN = 6;

    //attribute
    const SEARCH_BY_KEYWORD = 10;
    const MEAL_PLAN = 11;
    const HEALTHLAB_BRAND = 12;
}
