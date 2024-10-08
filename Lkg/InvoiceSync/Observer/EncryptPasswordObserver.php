<?php

/**
 * @author ICT
 * @copyright Copyright (c) 2024 Juni.com
 * @package A Magento 2 Invoice SYNC To LKG System Module.
 */

namespace Lkg\InvoiceSync\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class EncryptPasswordObserver implements ObserverInterface
{
    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor for the class.
     *
     * @param WriterInterface $configWriter
     * @param EncryptorInterface $encryptor
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        WriterInterface $configWriter,
        EncryptorInterface $encryptor,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->configWriter = $configWriter;
        $this->encryptor = $encryptor;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Constructor for the class.
     *
     * @param Observer $observer Instance for managing password value.
     */
    public function execute(Observer $observer)
    {
        $configData = $observer->getData('configData');
        $path = 'invoicesync/general/soap_pass';

        if (isset($configData[$path]) && $configData[$path] !== '******') {
            $encryptedPassword = $this->encryptor->encrypt($configData[$path]);
            $this->configWriter->save($path, $encryptedPassword);
        } elseif (!isset($configData[$path]) || $configData[$path] === '******') {
            $currentEncryptedPassword = $this->scopeConfig
            ->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($currentEncryptedPassword) {
                $this->configWriter->save($path, $currentEncryptedPassword);
            }
        }
    }
}
