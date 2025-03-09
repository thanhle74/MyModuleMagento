<?php
declare(strict_types=1);
namespace TTTech\Import\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    public const API_KEY       = 'tttech/general/api_key';
    public const API_SECRET    = 'tttech/general/secret';
    public const WEBSHOP       = 'tttech/general/webshop';
    public const STATUS     = 'tttech/general/enable';
    public const DEVELOPER     = 'tttech/developer/enable';
    public const LOOP     = 'tttech/developer/num_loop';
    public const SPECIFIC_ID     = 'tttech/developer/specific_id';
    public const CLEANUP_TIME_THRESHOLD = 'tttech/cron/cleanup_time_threshold';
    public const CRON_FREQUENCY = 'tttech/cron/cron_expression';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context                 $context,
        ScopeConfigInterface    $scopeConfig,
        StoreManagerInterface   $storeManager
    )
    {
        $this->scopeConfig  = $scopeConfig;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Summary of statusModule
     * @return mixed
     */
    public function statusModule()
    {
        return $this->getConfigData(self::STATUS);
    }

    /**
     * Summary of cleanupTimeThreshold
     * @return mixed
     */
    public function cleanupTimeThreshold()
    {
        return $this->getConfigData(self::CLEANUP_TIME_THRESHOLD);
    }

    /**
     * Summary of cronFrequency
     * @return mixed
     */
    public function cronFrequency()
    {
        return $this->getConfigData(self::CRON_FREQUENCY);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getApiKey(): string
    {
        return $this->getConfigData(self::API_KEY);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSecret(): string
    {
        return $this->getConfigData(self::API_SECRET);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getWebshop(): string
    {
        return $this->getConfigData(self::WEBSHOP);
    }

    /**
     * Summary of getDeveloper
     * @return int
     */
    public function getDeveloper(): int
    {
        return (int)$this->getConfigData(self::DEVELOPER);
    }

    /**
     * Summary of getLoop
     * @return int
     */
    public function getLoop(): int
    {
        return (int)$this->getConfigData(self::LOOP);
    }

    /**
     * Summary of getSpecificId
     * @return string
     */
    public function getSpecificId(): string
    {
        return $this->getConfigData(self::SPECIFIC_ID);
    }

    /**
     * @param string $path
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getConfigData(string $path): mixed
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore());
    }
}
