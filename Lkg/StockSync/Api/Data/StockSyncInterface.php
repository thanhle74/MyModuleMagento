<?php
declare(strict_types=1);
namespace Lkg\StockSync\Api\Data;

interface StockSyncInterface
{
    const SYNC_ID = 'sync_id';
    const PUBLISHER_ID = 'publisher_id';
    const ARTICLE_NUMBER = 'article_number';
    const STOCK_AVAILABLE = 'stock_available';
    const STOCK = 'stock';
    const DATED_WITH_RESERVATION = 'dated_with_reservation';
    const SYNC_STATUS = 'sync_status';
    const CREATED = 'created';
    const MESSAGE = 'message';

    /**
     * @return mixed
     */
    public function getId(): mixed;

    /**
     * @return int
     */
    public function getPublisherId(): int;

    /**
     * @return int
     */
    public function getArticleNumber(): int;

    /**
     * @return int
     */
    public function getStockAvailable(): int;

    /**
     * @return int
     */
    public function getStock(): int;

    /**
     * @return int
     */
    public function getDatedWithReservation(): int;

    /**
     * @return int
     */
    public function getSyncStatus(): int;

    /**
     * @return string
     */
    public function getCreated(): string;

    /**
     * @param $value
     * @return StockSyncInterface
     */
    public function setId($value): StockSyncInterface;

    /**
     * @param string $publisherId
     * @return StockSyncInterface
     */
    public function setPublisherId(string $publisherId): StockSyncInterface;

    /**
     * @param string $articleNumber
     * @return StockSyncInterface
     */
    public function setArticleNumber(string $articleNumber): StockSyncInterface;

    /**
     * @param int $stockAvailable
     * @return StockSyncInterface
     */
    public function setStockAvailable(int $stockAvailable): StockSyncInterface;

    /**
     * @param int $stock
     * @return StockSyncInterface
     */
    public function setStock(int $stock): StockSyncInterface;

    /**
     * @param int $datedWithReservation
     * @return StockSyncInterface
     */
    public function setDatedWithReservation(int $datedWithReservation): StockSyncInterface;

    /**
     * @param int $syncStatus
     * @return StockSyncInterface
     */
    public function setSyncStatus(int $syncStatus): StockSyncInterface;

    /**
     * @param string $created
     * @return StockSyncInterface
     */
    public function setCreated(string $created): StockSyncInterface;

    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @param string $message
     * @return StockSyncInterface
     */
    public function setMessage(string $message = null): StockSyncInterface;
}
