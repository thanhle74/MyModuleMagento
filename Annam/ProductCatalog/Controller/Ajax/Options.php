<?php
declare(strict_types=1);
namespace Annam\ProductCatalog\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Annam\HealthLab\Helper\Data;
use Magento\Eav\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Annam\HealthLab\Model\ProductAttributeValuesGetter;

class Options extends Action
{
    const TEMPLATE = "Annam_ProductCatalog::search/options.phtml";
    const BLOCK = "Magento\Framework\View\Element\Template";

    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var Data
     */
    protected Data $helperData;

    /**
     * @var Config
     */
    protected Config $eavConfig;

    /**
     * @var PageFactory
     */
    protected PageFactory $_resultPageFactory;

    /**
     * @var ProductAttributeValuesGetter
     */
    protected ProductAttributeValuesGetter $productAttributeValuesGetter;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param Context $context
     * @param Data $helperData
     * @param Config $eavConfig
     * @param PageFactory $_resultPageFactory
     * @param ProductAttributeValuesGetter $productAttributeValuesGetter
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        Context $context,
        Data $helperData,
        Config $eavConfig,
        PageFactory $_resultPageFactory,
        ProductAttributeValuesGetter $productAttributeValuesGetter
    )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helperData = $helperData;
        $this->eavConfig = $eavConfig;
        $this->_resultPageFactory = $_resultPageFactory;
        $this->productAttributeValuesGetter = $productAttributeValuesGetter;
    }

    /**
     * @return Json
     * @throws LocalizedException
     */
    public function execute()
    {
        $data = [];
        $keyword = $this->getRequest()->getParam('keyword');
        $attributeCode = $this->helperData->searchByKeyword();
        $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);

        if($attribute->getId())
        {
            $options = $attribute->getSource()->getAllOptions();
            if(count($options) > 1)
            {
                if($keyword == 'all')
                {
                    foreach ($options as $option) {
                        if (isset($option['value']) && trim($option['value'])) {
                            $countProducts = count($this->productAttributeValuesGetter->getListProducts($attributeCode, (int)$option['value']));
                            $data[] = [
                                'value' => $option['label'],
                                'value_id' => $option['value'],
                                'count' => $countProducts
                            ];
                        }
                    }
                }else{
                    $listOptions = $this->productAttributeValuesGetter->getAttributeValues((string)$keyword);
                    foreach($listOptions as $key => $value)
                    {
                        if(trim($value['value']))
                        {
                            $countProducts = count($this->productAttributeValuesGetter->getListProducts($attributeCode, (int)$value['option_id']));
                            $data[] = [
                                'value' => $value['value'],
                                'value_id' => $value['option_id'],
                                'count' => $countProducts
                            ];
                        }
                    }
                }
            }
        }

        $result = $this->resultJsonFactory->create();
        $resultPage = $this->_resultPageFactory->create();
        $block = $resultPage->getLayout()->createBlock(self::BLOCK)->setTemplate(self::TEMPLATE)->setData('data',$data)->toHtml();
        $result->setData(['output' => $block]);

        return $result;
    }
}
