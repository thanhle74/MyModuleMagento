<?php
declare(strict_types=1);
namespace Annam\SearchResult\Block;

use Magento\Framework\View\Element\Template\Context;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Eav\Model\Config;
use Annam\HealthLab\Helper\Data;

class Products extends Template
{

    /**
     * @var Config
     */
    protected Config $eavConfig;

    /**
     * @var Data
     */
    protected Data $helperData;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param Context $context
     * @param Config $eavConfig
     * @param Data $helperData
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $eavConfig,
        Data $helperData,
        LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData = $helperData;
        $this->eavConfig = $eavConfig;
        $this->logger = $logger;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getNameOption(): string
    {
        $result = '';
        $attributeCode = $this->helperData->searchByKeyword();
        try {
            $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
            $result = $attribute->getSource()->getOptionText($this->getParamId());
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getParamId(): int
    {
        return (int) $this->helperData->getParameterValue('id');
    }
}
