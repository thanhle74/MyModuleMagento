<?php
declare(strict_types=1);
namespace Annam\HealthLab\Api;

interface ProductHelperInterface
{
    /**
     * @param $product
     * @return string
     */
    public function getUrlImage($product): string;

}
