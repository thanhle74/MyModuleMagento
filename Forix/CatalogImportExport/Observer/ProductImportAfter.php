<?php
declare(strict_types=1);

namespace Forix\CatalogImportExport\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;
use Exception;
use Magento\ProductVideo\Model\Product\Attribute\Media\ExternalVideoEntryConverter;
use Forix\CatalogImportExport\Helper\Data as Helper;

class ProductImportAfter implements ObserverInterface
{
    /**
     * Summary of __construct
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Forix\CatalogImportExport\Helper\Data $helper
     */
    public function __construct(
        protected ResourceConnection $resource,
        protected LoggerInterface $logger,
        protected Helper $helper
    ) {}

    /**
     * Summary of execute
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            $bunch = $observer->getData('bunch');
            if (!is_array($bunch)) return;

            $skus = array_column($bunch, 'sku');
            if (empty($skus)) return;

            $connection = $this->resource->getConnection();
            $productIds = $this->getProductIdsBySkus($connection, $skus);
            if (empty($productIds)) return;

            $valueIds = $this->getMediaGalleryValueIds($connection, $productIds);
            if (empty($valueIds)) return;

            $mediaValues = $this->getMediaGalleryValues($connection, $valueIds);
            if (empty($mediaValues)) return;

            $videoRecords = $this->extractYouTubeVideos($mediaValues);
            if (!empty($videoRecords)) {
                $this->insertVideoRecords($connection, $videoRecords);
                $this->updateMediaTypeForVideos($connection, array_column($videoRecords, 'value_id'));
            }
        } catch (Exception $e) {
            $this->logger->error('ProductImportAfter Error: ' . $e->getMessage());
        }
    }

    /**
     * Summary of getProductIdsBySkus
     * @param mixed $connection
     * @param array $skus
     * @return array
     */
    private function getProductIdsBySkus($connection, array $skus): array
    {
        return $connection->fetchCol(
            $connection->select()
                ->from($this->resource->getTableName('catalog_product_entity'), ['entity_id'])
                ->where('sku IN (?)', $skus)
        );
    }

    /**
     * Summary of getMediaGalleryValueIds
     * @param mixed $connection
     * @param array $productIds
     * @return array
     */
    private function getMediaGalleryValueIds($connection, array $productIds): array
    {
        return $connection->fetchCol(
            $connection->select()
                ->from($this->resource->getTableName('catalog_product_entity_media_gallery_value'), ['value_id'])
                ->where('entity_id IN (?)', $productIds)
        );
    }

    /**
     * Summary of getMediaGalleryValues
     * @param mixed $connection
     * @param array $valueIds
     * @return array
     */
    private function getMediaGalleryValues($connection, array $valueIds): array
    {
        return $connection->fetchAll(
            $connection->select()
                ->from($this->resource->getTableName('catalog_product_entity_media_gallery'), ['value_id', 'value'])
                ->where('value_id IN (?)', $valueIds)
        );
    }

    private function extractYouTubeVideos(array $mediaValues): array
    {
        $videoRecords = [];
        foreach ($mediaValues as $media) {
            if (preg_match('/youtubeImage_[^_]+___(\w+)\.jpg$/', $media['value'], $matches)) {
                $videoId = $matches[1];
                $videoInfo = $this->helper->getYoutubeVideoInfo($videoId);
                $videoRecords[] = [
                    'value_id' => $media['value_id'],
                    'store_id' => 0,
                    'provider' => 'youtube',
                    'url' => 'https://www.youtube.com/watch?v=' . $videoId,
                    'title' => $videoInfo['title'] ?? '',
                    'description' => $videoInfo['description'] ?? '',
                    'metadata' => json_encode(['video_id' => $videoId]),
                ];
            }
        }
        return $videoRecords;
    }    

    /**
     * Summary of insertVideoRecords
     * @param mixed $connection
     * @param array $videoRecords
     * @return void
     */
    private function insertVideoRecords($connection, array $videoRecords): void
    {
        try {
            $connection->insertMultiple(
                $this->resource->getTableName('catalog_product_entity_media_gallery_value_video'),
                $videoRecords
            );
        } catch (Exception $e) {
            $this->logger->error('Error inserting video records: ' . $e->getMessage());
        }
    }

    /**
     * Summary of updateMediaTypeForVideos
     * @param mixed $connection
     * @param array $valueIds
     * @return void
     */
    private function updateMediaTypeForVideos($connection, array $valueIds): void
    {
        if (!empty($valueIds)) {
            try {
                $connection->update(
                    $this->resource->getTableName('catalog_product_entity_media_gallery'),
                    ['media_type' => ExternalVideoEntryConverter::MEDIA_TYPE_CODE],
                    ['value_id IN (?)' => $valueIds]
                );
            } catch (Exception $e) {
                $this->logger->error('Error updating media_type: ' . $e->getMessage());
            }
        }
    }
}
