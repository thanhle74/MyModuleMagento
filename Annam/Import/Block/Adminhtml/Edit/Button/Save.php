<?php
declare(strict_types=1);
namespace Annam\Import\Block\Adminhtml\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Ui\Component\Control\Container;
use Annam\HealthLab\Block\Adminhtml\Edit\Button\Generic;

class Save extends Generic implements ButtonProviderInterface
{
    const FORM = 'healthlab_import_mapping_form.healthlab_import_mapping_form';

    public function getButtonData(): array
    {
        return [
            'label' => __('Import Data'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
