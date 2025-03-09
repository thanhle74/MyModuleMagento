<?php
declare(strict_types=1);

namespace Forix\CatalogImportExport\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogImportExport\Model\Import\Product;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class RemoveOldImages
{
    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ProductFactory $productFactory
     */
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductFactory $productFactory
    ) {}

    /**
     * @param Product $subject
     * @param array $rowData
     * @return array[]
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function beforeGetImagesFromRow(Product $subject, array $rowData): array
    {
        $sku = $rowData['sku'] ?? null;
        if ($sku && isset($rowData['is_delete_image'])) {
            if (isset($rowData['is_delete_image']) == 1) {
                $productBySku = $this->productRepository->get($sku);
                $productId = $productBySku->getId();
                $productFactory = $this->productFactory->create();
                $existingMediaGalleryEntries = $productFactory->load($productId)->getMediaGalleryEntries();
                foreach ($existingMediaGalleryEntries as $key => $entry) {
                    unset($existingMediaGalleryEntries[$key]);
                }
                $productFactory->setMediaGalleryEntries($existingMediaGalleryEntries);
                $this->productRepository->save($productFactory);
            }
        }

        return [$rowData];
    }
}
