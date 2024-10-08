<?php
declare (strict_types = 1);
namespace Annam\HealthLab\Setup\Patch\Data;

use Exception;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\CategoryFactory;

class CreateMainHeathCategory implements DataPatchInterface
{
    const ECOMMERCE_CATALOG = 'ECOMMERCE CATALOG';

    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var CategoryFactory
     */
    private CategoryFactory $categoryFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategoryFactory $categoryFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return void
     * @throws Exception
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();

        $categoryId = 2;
        $category = $this->categoryFactory->create()->loadByAttribute('name', self::ECOMMERCE_CATALOG);
        if ($category) {
            $categoryId = $category->getId();
        }

        $path = '1/' . (string)$categoryId;
        $category = $this->categoryFactory->create();
        $category->setName('Main Health');
        $category->setIsActive(false);
        $category->setParentId($categoryId);
        $category->setPath($path);
        $category->setLevel(2);
        $category->setPosition(1);
        $category->setIncludeInMenu(true);
        $category->setAttributeSetId($category->getDefaultAttributeSetId());
        $category->save();

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}
