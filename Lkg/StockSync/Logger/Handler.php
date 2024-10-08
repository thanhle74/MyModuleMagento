<?php
declare(strict_types=1);
namespace Lkg\StockSync\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{

    /**
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * @var string
     */
    protected $fileName = '/var/log/lkg_stock_sync.log';
}
