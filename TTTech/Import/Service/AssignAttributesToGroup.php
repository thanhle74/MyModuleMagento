<?php
declare(strict_types=1);
namespace TTTech\Import\Service;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;

class AssignAttributesToGroup
{
    /**
     * @var EavSetupFactory
     */
    protected EavSetupFactory $eavSetupFactory;

    /**
     * @var ResourceConnection
     */
    protected ResourceConnection $resourceConnection;

    /**
     * AssignAttributesToGroup constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct
    (
        EavSetupFactory $eavSetupFactory,
        ResourceConnection $resourceConnection
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param string $attributeSetName
     * @param string $attributeGroupName
     * @param array $attributeCodes
     * @throws LocalizedException
     */
    public function execute(string $attributeSetName, string $attributeGroupName, array $attributeCodes): void
    {
        $eavSetup = $this->eavSetupFactory->create();
        $entityTypeId = $eavSetup->getEntityTypeId('catalog_product');
        $attributeSetId = $eavSetup->getAttributeSetId($entityTypeId, $attributeSetName);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, $attributeGroupName);

        foreach ($attributeCodes as $attributeCode) {
            $attributeId = $eavSetup->getAttributeId($entityTypeId, $attributeCode);

            if (!$attributeId) {
                throw new LocalizedException(__("Attribute code '%1' does not exist.", $attributeCode));
            }

            if (!$this->isAttributeInGroup($attributeId, $attributeSetId, $attributeGroupId)) {
                $eavSetup->addAttributeToGroup(
                    $entityTypeId,
                    $attributeSetId,
                    $attributeGroupId,
                    $attributeId,
                    null
                );
            }
        }
    }

    /**
     * @param $attributeId
     * @param $attributeSetId
     * @param $attributeGroupId
     * @return bool
     */
    private function isAttributeInGroup($attributeId, $attributeSetId, $attributeGroupId): bool
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from('eav_entity_attribute')
            ->where('attribute_id = ?', $attributeId)
            ->where('attribute_set_id = ?', $attributeSetId)
            ->where('attribute_group_id = ?', $attributeGroupId);

        $existingAttribute = $connection->fetchOne($select);

        return (bool)$existingAttribute;
    }
}
