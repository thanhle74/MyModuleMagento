<?php
declare(strict_types=1);
namespace Annam\Ingredient\Block\Adminhtml\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Ui\Component\Control\Container;
use Annam\HealthLab\Block\Adminhtml\Edit\Button\Generic;

class Save extends Generic implements ButtonProviderInterface
{
    const FORM = 'ingredient_form.ingredient_form';

    public function getButtonData(): array
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        $options[] = [
            'id_hard' => 'save_and_new',
            'label' => __('Save & New'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => self::FORM,
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'back' => 'add'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        $options[] = [
            'id_hard' => 'save_and_close',
            'label' => __('Save & Close'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => self::FORM,
                                'actionName' => 'save',
                                'params' => [
                                    true
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        return $options;
    }
}
