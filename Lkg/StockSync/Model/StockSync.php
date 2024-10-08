<?php
declare(strict_types=1);
namespace Lkg\StockSync\Model;

use Magento\Framework\Model\AbstractModel;
use Lkg\StockSync\Model\ResourceModel\StockSync as StockSyncResourceModel;
use Lkg\StockSync\Api\Data\StockSyncInterface;

class StockSync extends AbstractModel implements StockSyncInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(StockSyncResourceModel::class);
    }

    /**
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->getData(self::SYNC_ID);
    }

    /**
     * @return int
     */
    public function getPublisherId(): int
    {
        return $this->getData(self::PUBLISHER_ID);
    }

    /**
     * @return int
     */
    public function getArticleNumber(): int
    {
        return $this->getData(self::ARTICLE_NUMBER);
    }

    /**
     * @return int
     */
    public function getStockAvailable(): int
    {
        return $this->getData(self::STOCK_AVAILABLE);
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->getData(self::STOCK);
    }

    /**
     * @return int
     */
    public function getDatedWithReservation(): int
    {
        return $this->getData(self::DATED_WITH_RESERVATION);
    }

    /**
     * @return int
     */
    public function getSyncStatus(): int
    {
        return $this->getData(self::SYNC_STATUS);
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return $this->getData(self::CREATED);
    }

    /**
     * @param $value
     * @return StockSyncInterface
     */
    public function setId($value): StockSyncInterface
    {
        return $this->setData(self::SYNC_ID, $value);
    }

    /**
     * @param string $publisherId
     * @return StockSyncInterface
     */
    public function setPublisherId(string $publisherId): StockSyncInterface
    {
        return $this->setData(self::PUBLISHER_ID, $publisherId);
    }

    /**
     * @param string $articleNumber
     * @return StockSyncInterface
     */
    public function setArticleNumber(string $articleNumber): StockSyncInterface
    {
        return $this->setData(self::ARTICLE_NUMBER, $articleNumber);
    }

    /**
     * @param int $stockAvailable
     * @return StockSyncInterface
     */
    public function setStockAvailable(int $stockAvailable): StockSyncInterface
    {
        return $this->setData(self::STOCK_AVAILABLE, $stockAvailable);
    }

    /**
     * @param int $stock
     * @return StockSyncInterface
     */
    public function setStock(int $stock): StockSyncInterface
    {
        return $this->setData(self::STOCK, $stock);
    }

    /**
     * @param int $datedWithReservation
     * @return StockSyncInterface
     */
    public function setDatedWithReservation(int $datedWithReservation): StockSyncInterface
    {
        return $this->setData(self::DATED_WITH_RESERVATION, $datedWithReservation);
    }

    /**
     * @param int $syncStatus
     * @return StockSyncInterface
     */
    public function setSyncStatus(int $syncStatus): StockSyncInterface
    {
        return $this->setData(self::SYNC_STATUS, $syncStatus);
    }

    /**
     * @param string $created
     * @return StockSyncInterface
     */
    public function setCreated(string $created): StockSyncInterface
    {
        return $this->setData(self::CREATED, $created);
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @param string $message
     * @return StockSyncInterface
     */
    public function setMessage(string $message = null): StockSyncInterface
    {
        return $this->setData(self::MESSAGE, $message);
    }
}
