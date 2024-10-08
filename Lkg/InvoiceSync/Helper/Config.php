<?php

/**
 * @author ICT
 * @copyright Copyright (c) 2024 Juni.com
 * @package A Magento 2 Invoice SYNC To LKG System Module.
 */

namespace Lkg\InvoiceSync\Helper;

use \Magento\Store\Model\ScopeInterface;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const XML_PATH_WSDL_URL = 'invoicesync/general/wsdl_url';
    public const XML_PATH_SOAP_USER = 'invoicesync/general/soap_user';
    public const XML_PATH_SOAP_PASS = 'invoicesync/general/soap_pass';
    public const XML_PATH_SOAP_TARGET = 'invoicesync/general/soap_target';
    public const XML_PATH_PUBLISHER_ID = 'invoicesync/general/publisher_id';
    public const XML_PATH_ORDER_SOURCE = 'invoicesync/general/order_source';
    public const XML_PATH_STOCK_RESERVE = 'invoicesync/general/stock_reserve';
    /**
     * Retrieve configuration WSDLURL
     *
     * @return bool
     */
    public function getWsdlUrl()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WSDL_URL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the User Name.
     *
     * @return integer
     */
    public function getSoapUser()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SOAP_USER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the User Password.
     *
     * @return string
     */
    public function getSoapPass()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SOAP_PASS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the Target.
     *
     * @return int
     */
    public function getSoapTarget()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SOAP_TARGET, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the Publisher Id.
     *
     * @return int
     */
    public function getPublisherId()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PUBLISHER_ID, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the configured API URL.
     *
     * @return string
     */
    public function getOrderSource()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ORDER_SOURCE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the stock reserve.
     *
     * @return string
     */
    public function getStockReserveValue()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_STOCK_RESERVE, ScopeInterface::SCOPE_STORE);
    }
}
