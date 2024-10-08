<?php
declare (strict_types = 1);
namespace Annam\HealthLab\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Email\Model\TemplateFactory;

class ListEmailTemplate implements OptionSourceInterface
{
    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * @param TemplateFactory $templateFactory
     */
    public function __construct(TemplateFactory $templateFactory)
    {
        $this->templateFactory = $templateFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $templates = $this->templateFactory->create()->getCollection();
        $data = [];

        foreach ($templates as $template) {
            $data[] = [
                "value" => (int) $template->getId(),
                "label" => $template->getData("template_code"),
            ];
        }

        return $data;
    }
}
