<?php
namespace Annam\HealthLab\Api;

interface ProductAttributeValuesGetterInterface
{
    /**
     * @param String $searchValue
     * @return array
     */
    public function getAttributeValues(String $searchValue): array;

    /**
     * @param string $attributeCode
     * @param int $valueId
     * @param array $sort
     * @return array
     */
    public function getListProducts(string $attributeCode, int $valueId, array $sort = []): array;
}
