<?php
declare(strict_types=1);
namespace TTTech\Import\Service;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use TTTech\Import\Logger\Logger;

class ConfigurableProductService
{
    const ATTRIBUTE_CODE = 'size';

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var ProductFactory
     */
    protected ProductFactory $productFactory;

    /**
     * @var Logger
     */
    protected Logger $logger;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ProductFactory $productFactory
     * @param Logger $logger
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductFactory $productFactory,
        Logger $logger
    ) {
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->logger = $logger;
    }

    /**
     * @param $configProduct
     * @param $associatedProductIds
     * @param $sku
     * @return void
     */
    public function assignAssociatedProducts($configProduct, $associatedProductIds, $sku): void
    {
        $sizeAttrId = $configProduct->getResource()->getAttribute(self::ATTRIBUTE_CODE)->getId();
        $configProduct->getTypeInstance()->setUsedProductAttributeIds([$sizeAttrId], $configProduct);
        $configurableAttributesData = $configProduct->getTypeInstance()->getConfigurableAttributesAsArray($configProduct);
        $configProduct->setCanSaveConfigurableAttributes(true);
        $configProduct->setConfigurableAttributesData($configurableAttributesData);
        $configProduct->setConfigurableProductsData([]);

        try {
            $this->productRepository->save($configProduct);
        } catch (\Exception $e) {
            $this->logger->error('Error creating configurable product: ' . $e->getMessage());
        }

        $productId = $configProduct->getId();
        try {
            $configurableProduct = $this->productRepository->getById($productId);
            $configurableProduct->setAssociatedProductIds($associatedProductIds);
            $configurableProduct->setCanSaveConfigurableAttributes(true);
            $this->productRepository->save($configurableProduct);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage() .' No associated products found for configurable product with SKU: ' . $sku);
        }
    }
}
