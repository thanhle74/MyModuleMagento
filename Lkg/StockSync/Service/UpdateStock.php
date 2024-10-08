<?php
declare(strict_types=1);

namespace Lkg\StockSync\Service;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class UpdateStock
{
    /**
     * @var StockRegistryInterface
     */
    protected StockRegistryInterface $stockRegistry;

    /**
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct
    (
        StockRegistryInterface $stockRegistry
    )
    {
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * @param string $sku
     * @param int $qty
     * @return void
     * @throws NoSuchEntityException
     */
    public function handleStockUpdate(string $sku, int $qty): void
    {
        $stockItem = $this->stockRegistry->getStockItemBySku($sku);
        $stockItem->setQty($qty);
        $stockItem->setManageStock(false);
        $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
    }
}
