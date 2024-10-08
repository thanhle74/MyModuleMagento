<?php
declare(strict_types=1);
namespace Lkg\Base\Service;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;

class AttributeUpdater
{
    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var AttributeRepositoryInterface
     */
    protected AttributeRepositoryInterface $attributeRepository;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param AttributeRepositoryInterface $attributeRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        AttributeRepositoryInterface $attributeRepository,
        LoggerInterface $logger
    ) {
        $this->productRepository = $productRepository;
        $this->attributeRepository = $attributeRepository;
        $this->logger = $logger;
    }

    /**
     * @param string $sku
     * @param string $attributeCode
     * @param string $optionLabel
     * @return void
     */
    public function updateDropdownAttribute(string $sku, string $attributeCode ,string $optionLabel): void
    {
        try {
            $product = $this->productRepository->get($sku);

            // Load attribute options
            $attribute = $this->attributeRepository->get('catalog_product', $attributeCode);
            $options = $attribute->getSource()->getAllOptions();

            // Find the option ID based on the option label
            $optionId = null;
            foreach ($options as $option) {
                if ($option['label'] == $optionLabel) {
                    $optionId = $option['value'];
                    break;
                }
            }

            if ($optionId === null) {
                throw new \Exception("Option label '{$optionLabel}' not found");
            }

            // Set the custom attribute value (option ID)
            $product->setCustomAttribute($attributeCode, $optionId);
            $this->productRepository->save($product);

        } catch (NoSuchEntityException $e)
        {
            $this->logger->info("Product with SKU '{$sku}' does not exist.");
        } catch (CouldNotSaveException $e)
        {
            $this->logger->info("Could not save the product: " . $e->getMessage());
        } catch (\Exception $e)
        {
            $this->logger->info($e->getMessage());
        }
    }
}
