<?php
declare(strict_types=1);
namespace Annam\Import\Service\Attribute;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;

class SearchByKeyword extends AbstractImportAttribute
{
    /**
     * @param array $importData
     * @return void
     * @throws InputException
     * @throws LocalizedException
     * @throws StateException
     */
    public function handle(array $importData)
    {
        $attributeCode = $this->annamHelper->searchByKeyword();
        $this->import($importData, $attributeCode);
    }
}
