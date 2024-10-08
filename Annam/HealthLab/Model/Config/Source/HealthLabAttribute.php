<?php
declare(strict_types=1);
namespace Annam\HealthLab\Model\Config\Source;

use Magento\Eav\Api\AttributeRepositoryInterface;
use Annam\HealthLab\Helper\Data as AnnamHelper;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class HealthLabAttribute implements OptionSourceInterface
{
    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var AnnamHelper
     */
    protected $annamHelper;

    /**
     * @param AttributeRepositoryInterface $attributeRepository
     * @param AnnamHelper $annamHelper
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        AnnamHelper $annamHelper
    )
    {
        $this->attributeRepository = $attributeRepository;
        $this->annamHelper = $annamHelper;
    }


    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [];
    }

    /**
     * @param string $attributeCode
     * @return array
     * @throws NoSuchEntityException
     */
    public function getAllOptions(string $attributeCode): array
    {
        $attribute = $this->attributeRepository->get('catalog_product', $attributeCode);
        $options = $attribute->getSource()->getAllOptions();
        $data = [];
        foreach ($options as $option) {
            if (isset($option['value']) && trim($option['value'])) {
                $data[] = [
                    'value' => $option['value'],
                    'label' => $option['label'],
                ];
            }
        }

        return $data;
    }
}
