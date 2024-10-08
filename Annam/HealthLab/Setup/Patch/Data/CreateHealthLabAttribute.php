<?php
namespace Annam\HealthLab\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;

class CreateHealthLabAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Apply patch
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $listAttributes = [
            'search_by_keyword' => 'Search By Keyword',
            'meal_plan' => 'Attribute Meal Plan',
        ];

        foreach ($listAttributes as $attributeCode => $attributeLabel)
        {
            $eavSetup->removeAttribute(Product::ENTITY, $attributeCode);
            $eavSetup->addAttribute(
                Product::ENTITY,
                $attributeCode,
                [
                    'type' => 'varchar',
                    'label' => $attributeLabel,
                    'input' => 'multiselect',
                    'required' => false,
                    'sort_order' => 30,
                    'source' => '',
                    'global' => ScopedAttributeInterface::SCOPE_STORE,
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'filterable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'group' => 'General',
                    'default' => '',
                    'searchable' => false,
                    'comparable' => false,
                    'unique' => false,
                    'can_show' => 0
                ]
            );
        }

        $attributeCode = 'infographic';
        $eavSetup->removeAttribute(Product::ENTITY, $attributeCode);
        $eavSetup->addAttribute(
            Product::ENTITY,
            $attributeCode,
            [
                'type' => 'int',
                'label' => 'Infographic',
                'input' => 'boolean',
                'source' => Boolean::class,
                'required' => false,
                'default' => 0,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General',
                'filterable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'searchable' => false,
                'comparable' => false,
                'unique' => false,
                'can_show' => 0,
                'sort_order' => 30
            ]
        );

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Get dependencies
     *
     * @return string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
