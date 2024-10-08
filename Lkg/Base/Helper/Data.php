<?php
declare(strict_types=1);
namespace Lkg\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    public const SOAP_USER = 'lkg_general/config_general/soap_user';
    public const SOAP_PASS = 'lkg_general/config_general/soap_pass';
    public const STATUS_HISTORY_CRON = 'lkg_general/config_general/status_history_cron';

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
    public function isStatusHistoryCron(): string
    {
        return $this->getValueByPath(self::STATUS_HISTORY_CRON);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function soapUser(): string
    {
        return $this->getValueByPath(self::SOAP_USER);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function soapPassword(): string
    {
        return $this->getValueByPath(self::SOAP_PASS);
    }

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
}
