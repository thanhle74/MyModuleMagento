<?php
declare(strict_types=1);

namespace Lkg\StockSync\Service;

use Lkg\StockSync\Api\StockAvailabilityStatusInterface;
use Lkg\StockSync\Helper\Data as StockSyncHelper;
use Lkg\Base\Service\AttributeUpdater;

class UpdateProductAvailabilityStatus
{
    public const ATTRIBUTE_CODE = 'availability_status';

    /**
     * @var StockSyncHelper
     */
    protected StockSyncHelper $stockSyncHelper;

    /**
     * @var AttributeUpdater
     */
    protected AttributeUpdater $attributeUpdater;

    /**
     * @param StockSyncHelper $stockSyncHelper
     * @param AttributeUpdater $attributeUpdater
     */
    public function __construct
    (
        StockSyncHelper  $stockSyncHelper,
        AttributeUpdater $attributeUpdater
    )
    {
        $this->stockSyncHelper = $stockSyncHelper;
        $this->attributeUpdater = $attributeUpdater;
    }

    /**
     * @param $product
     * @param int $qty
     * @param string $publishingYear
     * @return void
     */
    public function handleUpdateAvailabilityStatus($product, int $qty, string $publishingYear): void
    {
        $status = StockAvailabilityStatusInterface::SOFORT_LIEFERBAR;
        $sku = $product->getSku();

        if ($this->stockSyncHelper->isFuture($publishingYear)) {
            $status = StockAvailabilityStatusInterface::NOCH_NICHT_ERSCHIENEN;
        }
        else if ($qty == 0) {
            if ($product->getAttributeText(self::ATTRIBUTE_CODE) == StockAvailabilityStatusInterface::VERGRIFFEN)
            {
                $status = StockAvailabilityStatusInterface::VERGRIFFEN;
            }
            else {
                $status = StockAvailabilityStatusInterface::KURZFRISTIG_NICHT_AM_LAGER;
            }
        }

        $this->attributeUpdater->updateDropdownAttribute($sku, self::ATTRIBUTE_CODE, $status);
    }
}
