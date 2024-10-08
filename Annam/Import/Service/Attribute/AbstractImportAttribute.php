<?php
declare(strict_types=1);
namespace Annam\Import\Service\Attribute;

use Magento\Eav\Model\Config;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use Annam\HealthLab\Helper\Data as AnnamHelper;

class AbstractImportAttribute
{
    /**
     * @var AnnamHelper
     */
    public AnnamHelper $annamHelper;

    /**
     * @var Config
     */
    protected Config $eavConfig;

    /**
     * @var AttributeRepositoryInterface
     */
    protected AttributeRepositoryInterface $attributeRepository;

    /**
     * @var AttributeOptionManagementInterface
     */
    protected AttributeOptionManagementInterface $optionManagement;

    /**
     * @var AttributeOptionInterfaceFactory
     */
    protected AttributeOptionInterfaceFactory $optionFactory;

    /**
     * @var AttributeOptionLabelInterfaceFactory
     */
    protected AttributeOptionLabelInterfaceFactory $optionLabelFactory;

    /**
     * @param Config $config
     * @param AttributeRepositoryInterface $attributeRepository
     * @param AttributeOptionManagementInterface $optionManagement
     * @param AttributeOptionLabelInterfaceFactory $optionLabelFactory
     * @param AttributeOptionInterfaceFactory $optionFactory
     * @param AnnamHelper $annamHelper
     */
    public function __construct
    (
        Config $config,
        AttributeRepositoryInterface $attributeRepository,
        AttributeOptionManagementInterface $optionManagement,
        AttributeOptionLabelInterfaceFactory $optionLabelFactory,
        AttributeOptionInterfaceFactory $optionFactory,
        AnnamHelper $annamHelper
    )
    {
        $this->eavConfig = $config;
        $this->attributeRepository = $attributeRepository;
        $this->optionManagement = $optionManagement;
        $this->optionLabelFactory = $optionLabelFactory;
        $this->optionFactory = $optionFactory;
        $this->annamHelper = $annamHelper;
    }

    /**
     * @param array $importData
     * @param string $attributeCode
     * @return void
     * @throws InputException
     * @throws LocalizedException
     * @throws StateException
     */
    public function import(array $importData, string $attributeCode)
    {
        $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
        $options = $attribute->getSource()->getAllOptions();

        /**
         * Tạo danh sách các tùy chọn hiện có để kiểm tra và tránh trùng lặp
         */
        $existingOptions = [];
        foreach ($options as $option) {
            if (!empty($option['label'])) {
                $existingOptions[$option['label']] = $option['value'];
            }
        }

        foreach ($importData as $label) {
            $label = $label[0];
            if (!array_key_exists($label, $existingOptions)) {
                $option = $this->optionFactory->create();
                $option->setLabel($label);
                $option->setValue('');

                $this->optionManagement->add('catalog_product', $attributeCode, $option);
            }
        }
    }
}
