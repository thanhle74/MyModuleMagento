<?php
declare(strict_types=1);
namespace Annam\HealthLab\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Annam\HealthLab\Helper\Data as AnnamHelper;
use Annam\HealthLab\Model\Config\Source\HealthLabAttribute as SourceHealthLabAttribute;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\Select;

class HealthLabAttribute extends AbstractModifier
{
    /**
     * @var AnnamHelper
     */
    protected AnnamHelper $annamHelper;

    /**
     * @var SourceHealthLabAttribute
     */
    protected SourceHealthLabAttribute $optionHealthLabAttribute;

    /**
     * @param AnnamHelper $annamHelper
     * @param SourceHealthLabAttribute $optionHealthLabAttribute
     */
    public function __construct
    (
        AnnamHelper $annamHelper,
        SourceHealthLabAttribute $optionHealthLabAttribute
    )
    {
        $this->annamHelper = $annamHelper;
        $this->optionHealthLabAttribute = $optionHealthLabAttribute;
    }

    /**
     * @param array $meta
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function modifyMeta(array $meta): array
    {
        $searchByKeyword = $this->annamHelper->searchByKeyword();
        $attributeCodeIngredients = $this->annamHelper->mealPlanAttribute();
        $attributeCodeInfographic = $this->annamHelper->getAttributeInfographic();
        $brand = $this->annamHelper->brand();

        $meta['healthlab_fieldset'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('HealthLab'),
                        'sortOrder' => 50,
                        'collapsible' => true,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => 'data.product'
                    ]
                ]
            ],
            'children' => [
                $searchByKeyword => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => Select::NAME,
                                'componentType' => Field::NAME,
                                'options' => $this->optionHealthLabAttribute->getAllOptions($searchByKeyword),
                                'visible' => 1,
                                'required' => 1,
                                'label' => __('Search By Keyword'),
                                'component' => 'Magento_Ui/js/form/element/ui-select',
                                'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                'multiple' => 1,
                                'filterOptions' => 1,
                                'showCheckbox' => 1,
                                'disableLabel' => 1,
                            ]
                        ]
                    ]
                ],
                $attributeCodeIngredients => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => Select::NAME,
                                'componentType' => Field::NAME,
                                'options' => $this->optionHealthLabAttribute->getAllOptions($attributeCodeIngredients),
                                'visible' => 1,
                                'required' => 1,
                                'label' => __('Meal Plan'),
                                'component' => 'Magento_Ui/js/form/element/ui-select',
                                'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                'multiple' => 1,
                                'filterOptions' => 1,
                                'showCheckbox' => 1,
                                'disableLabel' => 1,
                            ]
                        ]
                    ]
                ],
                $brand => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => Select::NAME,
                                'componentType' => Field::NAME,
                                'options' => $this->optionHealthLabAttribute->getAllOptions($brand),
                                'visible' => 1,
                                'required' => 1,
                                'label' => __('Brand'),
                                'component' => 'Magento_Ui/js/form/element/ui-select',
                                'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                'multiple' => 1,
                                'filterOptions' => 1,
                                'showCheckbox' => 1,
                                'disableLabel' => 1,
                            ]
                        ]
                    ]
                ],
                $attributeCodeInfographic => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => Checkbox::NAME,
                                'componentType' => Field::NAME,
                                'prefer' => 'toggle',
                                'label' => 'Infographic',
                                'showCheckbox' => 1,
                                'valueMap' => [
                                    'false' => '0',
                                    'true' => '1'
                                ]
                             ]
                        ]
                    ]
                ]
            ]
        ];

        return $meta;
    }


    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data): array
    {
        return $data;
    }
}
