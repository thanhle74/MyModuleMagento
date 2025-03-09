<?php
declare(strict_types=1);
namespace TTTech\Import\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    protected $loggerType = Logger::INFO;
    protected $fileName = '/var/log/tttech-import-product.log';
}
