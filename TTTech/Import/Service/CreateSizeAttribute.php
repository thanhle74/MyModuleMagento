<?php
declare(strict_types=1);
namespace TTTech\Import\Service;

use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validator\ValidateException;
use Psr\Log\LoggerInterface;

class CreateSizeAttribute
{
    const ATTRIBUTE_CODE = 'size';

    /**
     * @var AttributeRepositoryInterface
     */
    protected AttributeRepositoryInterface $attributeRepository;

    /**
     * @var EavSetupFactory
     */
    protected EavSetupFactory $eavSetupFactory;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param AttributeRepositoryInterface $attributeRepository
     * @param EavSetupFactory $eavSetupFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        EavSetupFactory $eavSetupFactory,
        LoggerInterface $logger
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->logger = $logger;
    }

    /**
     * @return bool
     * @throws ValidateException
     */
    public function create(): bool
    {
        if ($this->attributeExists()) {
            return true;
        }

        return $this->createAttribute();
    }

    /**
     * @return bool
     */
    private function attributeExists(): bool
    {
        try {
            $this->attributeRepository->get('catalog_product', self::ATTRIBUTE_CODE);
            return true;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @return bool
     * @throws ValidateException
     */
    private function createAttribute(): bool
    {
        try {
            $eavSetup = $this->eavSetupFactory->create(['setup' => null]);

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                self::ATTRIBUTE_CODE,
                [
                    'type' => 'varchar',
                    'label' => 'Size',
                    'input' => 'swatch_text',
                    'required' => false,
                    'user_defined' => true,
                    'default' => '',
                    'visible' => true,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible_on_front' => true,
                    'used_in_product_listing' => true,
                    'sort_order' => 100,
                    'option' => [
                        'values' => ['Small']
                    ]
                ]
            );

            return true;
        } catch (LocalizedException $e) {
            $this->logger->error("Error creating attribute '" . self::ATTRIBUTE_CODE . "': " . $e->getMessage());
            return false;
        }
    }
}
