<?php
declare(strict_types=1);

namespace ThanhAloha\CustomSourceSelection\Model\Algorithms;

use Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface;
use Magento\InventorySourceSelectionApi\Api\Data\ItemRequestInterface;
use Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface;
use Magento\InventorySourceSelectionApi\Model\SourceSelectionInterface;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventorySourceSelectionApi\Model\Algorithms\Result\GetDefaultSortedSourcesResult;

class PriorityBasedAlgorithm implements SourceSelectionInterface
{
    /**
     * @var GetSourceItemsBySkuInterface
     */
    private GetSourceItemsBySkuInterface $getSourceItemsBySku;

    /**
     * @var GetDefaultSortedSourcesResult
     */
    private GetDefaultSortedSourcesResult $getDefaultSortedSourcesResult;

    /**
     * @param GetSourceItemsBySkuInterface $getSourceItemsBySku
     * @param GetDefaultSortedSourcesResult $getDefaultSortedSourcesResult
     */
    public function __construct(
        GetSourceItemsBySkuInterface $getSourceItemsBySku,
        GetDefaultSortedSourcesResult $getDefaultSortedSourcesResult
    ) {
        $this->getSourceItemsBySku = $getSourceItemsBySku;
        $this->getDefaultSortedSourcesResult = $getDefaultSortedSourcesResult;
    }

    /**
     * @param InventoryRequestInterface $inventoryRequest
     * @return SourceSelectionResultInterface
     */
    public function execute(InventoryRequestInterface $inventoryRequest): SourceSelectionResultInterface
    {
        $selectedSources = [];

        foreach ($inventoryRequest->getItems() as $itemRequest) {
            /** @var ItemRequestInterface $itemRequest */
            $sku = $itemRequest->getSku();
            $sourceItems = $this->getSourceItemsBySku->execute($sku);

            $validSources = [];
            foreach ($sourceItems as $sourceItem) {
                if ($sourceItem->getQuantity() > 0) {
                    $validSources[] = $sourceItem;
                }
            }

            usort($validSources, function ($a, $b) {
                return $b->getQuantity() <=> $a->getQuantity();
            });

            foreach ($validSources as $sourceItem) {
                $selectedSources[] = $sourceItem;
            }
        }

        return $this->getDefaultSortedSourcesResult->execute($inventoryRequest, $selectedSources);
    }
}
