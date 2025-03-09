<?php
declare(strict_types=1);
namespace TTTech\Import\Service;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Eav\Model\Entity\Attribute\Source\TableFactory;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class OptionAttributeService
{
    /**
     * @var ProductAttributeRepositoryInterface
     */
    protected ProductAttributeRepositoryInterface $attributeRepository;

    /**
     * @var array
     */
    protected array $attributeValues;

    /**
     * @var TableFactory
     */
    protected TableFactory $tableFactory;

    /**
     * @var AttributeOptionManagementInterface
     */
    protected AttributeOptionManagementInterface $attributeOptionManagement;

    /**
     * @var AttributeOptionLabelInterfaceFactory
     */
    protected AttributeOptionLabelInterfaceFactory $optionLabelFactory;

    /**
     * @var AttributeOptionInterfaceFactory
     */
    protected AttributeOptionInterfaceFactory $optionFactory;

    /**
     * @param ProductAttributeRepositoryInterface $attributeRepository
     * @param TableFactory $tableFactory
     * @param AttributeOptionManagementInterface $attributeOptionManagement
     * @param AttributeOptionLabelInterfaceFactory $optionLabelFactory
     * @param AttributeOptionInterfaceFactory $optionFactory
     */
    public function __construct
    (
        ProductAttributeRepositoryInterface $attributeRepository,
        TableFactory $tableFactory,
        AttributeOptionManagementInterface $attributeOptionManagement,
        AttributeOptionLabelInterfaceFactory $optionLabelFactory,
        AttributeOptionInterfaceFactory $optionFactory
    )
    {

        $this->attributeRepository = $attributeRepository;
        $this->tableFactory = $tableFactory;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->optionLabelFactory = $optionLabelFactory;
        $this->optionFactory = $optionFactory;
    }

    public function getAttribute($attributeCode)
    {
        return $this->attributeRepository->get($attributeCode);
    }

    /**
     * @param $attributeCode
     * @param $label
     * @return false|mixed
     * @throws NoSuchEntityException
     * @throws InputException
     * @throws LocalizedException
     * @throws StateException
     */
    public function createOrGetId($attributeCode, $label): mixed
    {
        if (strlen($label) < 1) {
            throw new LocalizedException(
                __('Label for %1 must not be empty.', $attributeCode)
            );
        }

        $optionId = $this->getOptionId($attributeCode, $label);
        if (!$optionId) {
            $optionLabelAdmin = $this->optionLabelFactory->create();
            $optionLabelAdmin->setStoreId(0); // Admin store scope
            $optionLabelAdmin->setLabel($label);

            $optionLabelDefault = $this->optionLabelFactory->create();
            $optionLabelDefault->setStoreId(1); // Default store view scope
            $optionLabelDefault->setLabel($label);

            $option = $this->optionFactory->create();
            $option->setLabel($optionLabelAdmin->getLabel());
            $option->setStoreLabels([$optionLabelAdmin, $optionLabelDefault]);

            $option->setSortOrder(0);
            $option->setIsDefault(false);

            try {
                $this->attributeOptionManagement->add(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $this->getAttribute($attributeCode)->getAttributeId(),
                    $option
                );

                $optionId = $this->getOptionId($attributeCode, $label, true);
            } catch (LocalizedException $e) {
                if (str_contains($e->getMessage(), 'already exists')) {
                    $optionId = $this->getOptionId($attributeCode, $label);
                } else {
                    throw $e;
                }
            }
        }

        return $optionId;
    }


    /**
     * @param $attributeCode
     * @param $label
     * @param bool $force
     * @return false|mixed
     */
    private function getOptionId($attributeCode, $label, bool $force = false): mixed
    {
        $attribute = $this->getAttribute($attributeCode);

        // Build option array if necessary
        if ($force === true || !isset($this->attributeValues[ $attribute->getAttributeId() ])) {
            $this->attributeValues[ $attribute->getAttributeId() ] = [];
            $sourceModel = $this->tableFactory->create();
            $sourceModel->setAttribute($attribute);

            foreach ($sourceModel->getAllOptions() as $option) {
                $this->attributeValues[ $attribute->getAttributeId() ][ $option['label'] ] = $option['value'];
            }
        }

        // Return option ID if exists
        if (isset($this->attributeValues[ $attribute->getAttributeId() ][ $label ])) {
            return $this->attributeValues[ $attribute->getAttributeId() ][ $label ];
        }

        // Return false if does not exist
        return false;
    }
}
