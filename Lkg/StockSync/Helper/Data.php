<?php
declare(strict_types=1);
namespace Lkg\StockSync\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    public const SOAP_TARGET = 'lkg_general/stocksync/soap_target';
    public const WSDL_URL = 'lkg_general/stocksync/wsdl_url';
    public const PUBLISHER_ID = 'lkg_general/stocksync/publisher_id';
    public const STATUS_MODULE = 'lkg_general/stocksync/status_module';
    public const CLEANUP_TIME_THRESHOLD = 'lkg_general/stocksync/cleanup_time_threshold';
    public const CRON_FREQUENCY = 'lkg_general/stocksync/cron_expression';

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $_storeManager;

    /**
     * @param Context $context
     * @param StoreManagerInterface $_storeManager
     */
    public function __construct
    (
        Context               $context,
        StoreManagerInterface $_storeManager
    )
    {
        $this->_storeManager = $_storeManager;
        parent::__construct($context);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function cronFrequency(): string
    {
        return $this->getValueByPath(self::CRON_FREQUENCY);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function cleanupTimeThreshold(): string
    {
        return $this->getValueByPath(self::CLEANUP_TIME_THRESHOLD);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function statusModule(): string
    {
        return $this->getValueByPath(self::STATUS_MODULE);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function wsdlUrl(): string
    {
        return $this->getValueByPath(self::WSDL_URL);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function soapTarget(): string
    {
        return $this->getValueByPath(self::SOAP_TARGET);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function publisherId(): string
    {
        return $this->getValueByPath(self::PUBLISHER_ID);
    }

    //Base

    /**
     * @param string $path
     * @return string
     * @throws NoSuchEntityException
     */
    private function getValueByPath(string $path): string
    {
        return trim((string)$this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $this->getStoreCode()));
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getStoreCode(): string
    {
        return $this->_storeManager->getStore()->getCode();
    }

    /**
     * @param string $dataDateStr
     * @return bool
     */
    public function isFuture(string $dataDateStr): bool
    {
        $dataDate = \DateTime::createFromFormat('Y-m-d H:i:s', $dataDateStr);
        if ($dataDate === false) {
            return false;
        }

        $today = new \DateTime;
        return $dataDate > $today;
    }
}
