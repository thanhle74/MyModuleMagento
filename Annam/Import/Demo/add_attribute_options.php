<?php
use Magento\Framework\App\Bootstrap;
use Magento\Framework\Exception\LocalizedException;

require 'app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$obj = $bootstrap->getObjectManager();

$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

$eavConfig = $obj->get('Magento\Eav\Model\Config');
$attributeRepository = $obj->get('Magento\Eav\Api\AttributeRepositoryInterface');

$attributeCode = 'search_by_keyword'; // Thay thế bằng mã thuộc tính của bạn

try {
    $attribute = $eavConfig->getAttribute('catalog_product', $attributeCode);
    $options = $attribute->getSource()->getAllOptions();

    // Tạo danh sách các tùy chọn hiện có để kiểm tra và tránh trùng lặp
    $existingOptions = [];
    foreach ($options as $option) {
        if (!empty($option['label'])) {
            $existingOptions[$option['label']] = $option['value'];
        }
    }

    // Các tùy chọn mới cần thêm
    $newOptions = [
        'Option 1',
        'Option 2',
        'Option 3'
    ];

    $optionManagement = $obj->get('Magento\Eav\Api\AttributeOptionManagementInterface');
    $optionFactory = $obj->get('Magento\Eav\Api\Data\AttributeOptionInterfaceFactory');
    $optionLabelFactory = $obj->get('Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory');

    foreach ($newOptions as $label) {
        if (!array_key_exists($label, $existingOptions)) {
            $option = $optionFactory->create();
            $option->setLabel($label);
            $option->setValue('');

            $optionManagement->add('catalog_product', $attributeCode, $option);
            echo "Added option: $label\n";
        } else {
            echo "Option already exists: $label\n";
        }
    }

    echo "Options updated successfully.\n";
} catch (LocalizedException $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
