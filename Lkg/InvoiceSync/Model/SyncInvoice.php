<?php

/**
 * @author ICT
 * @copyright Copyright (c) 2024 Juni.com
 * @package A Magento 2 Invoice SYNC To LKG System Module.
 */

namespace Lkg\InvoiceSync\Model;

use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Lkg\InvoiceSync\Model\Sync;
use Magento\Sales\Model\OrderFactory;
use Lkg\InvoiceSync\Helper\Config;
use Lkg\InvoiceSync\Helper\ShortCountryCode;

class SyncInvoice
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderrepositoryinterface;
    /**
     * @var Sync
     */
    protected $sync;
    /**
     * @var OrderFactory
     */
    protected $orderfactory;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var ShortCountryCode
     */
    protected $shortCountryCode;

    /**
     * Constructor for the class.
     *
     * @param \Psr\Log\LoggerInterface $logger Logger instance for logging messages and errors.
     * @param OrderRepositoryInterface $orderrepositoryinterface Interface for order repository to manage order data.
     * @param Sync $sync Instance of the Sync class for synchronization operations.
     * @param OrderFactory $orderfactory Factory class for creating order instances.
     * @param Config $config Configuration instance for accessing module settings.
     * @param ShortCountryCode $shortCountryCode Instance for managing short country codes.
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        OrderRepositoryInterface $orderrepositoryinterface,
        Sync $sync,
        OrderFactory $orderfactory,
        Config $config,
        ShortCountryCode $shortCountryCode
    ) {
        $this->logger = $logger;
        $this->orderrepositoryinterface = $orderrepositoryinterface;
        $this->sync = $sync;
        $this->orderfactory = $orderfactory;
        $this->config = $config;
        $this->shortCountryCode = $shortCountryCode;
    }
    
    /**
     * Prepare Invoice Data
     *
     * @param array $invoice
     * @return boolean
     */
    public function processInvoiceData($invoice)
    {
        // $invoice = $observer->getData('invoice');
        $purchaseOrderNumber = '';
        $order = $invoice->getOrder();
        $orderId = $invoice->getOrderId();
        $invoiceData = $this->orderrepositoryinterface->get($orderId)->getData();
        $addressData = $this->getAddress($order->getBillingAddress());
        $shippingAddressData = $this->getAddress($order->getShippingAddress());
        $xmlData = $this->buildInvoiceXml($invoiceData, $addressData, $shippingAddressData);
        // @codingStandardsIgnoreStart
        $soapClient = new \SoapClient($this->getWsdlUrl(), [
            'trace' => true,
            'login' => $this->config->getSoapUser(),
            'password' => $this->config->getSoapPass(),
        ]);
        // @codingStandardsIgnoreEnd
        $soapRequest = [
            'xmlDaten' => '<![CDATA[' . $xmlData . ']]>',
            'ziel' => $this->config->getSoapTarget(),
        ];
        $result = $soapClient->__doRequest($xmlData, $this->getWsdlUrl(), 'insertOrder', SOAP_1_2, 0);
        
        // @codingStandardsIgnoreStart
        $doc = new \DOMDocument();
        // @codingStandardsIgnoreEnd
        $doc->loadXML($result);

        // Find the insertOrderResponse element
        $insertOrderResponseElement = $doc->getElementsByTagNameNS('vasws.GPAuftrag', 'insertOrderResponse')->item(0);

        // Check if the insertOrderResponse element exists
        if ($insertOrderResponseElement) {
            // Find the return element
            $returnElement = $insertOrderResponseElement->getElementsByTagName('return')->item(0);
        
            
            // Check if the return element exists
            if ($returnElement) {
                // Get the XML content from the return element
                $returnXml = $returnElement->textContent;
                
                // Load the return XML content
                // @codingStandardsIgnoreStart
                $returnDoc = new \DOMDocument();
                // @codingStandardsIgnoreEnd
                $returnDoc->loadXML($returnXml);
                
                // Find the purchase_order_number element
                $purchaseOrderNumberElement = $returnDoc->getElementsByTagName('purchase_order_number')->item(0);
                
                // Check if the purchase_order_number element exists
                if ($purchaseOrderNumberElement) {
                    // Get the purchase order number
                    $purchaseOrderNumber = $purchaseOrderNumberElement->textContent;
                }
            }
        }
       
        $sync = $this->sync->load($orderId, 'sync_order_id');
        $sync->setIncrementId($order['increment_id']);
        $sync->setCustomerName($order['customer_firstname']);
        $sync->setPaymentAmount($order['grand_total']);
        $sync->setSyncOrderId($order['entity_id']);
        $sync->setRequest($xmlData);
        $sync->setResponse($result);
        $sync->save();
        
        if ($result !== false && $purchaseOrderNumber != '') {
            $sync = $this->sync->load($orderId, 'sync_order_id');
            $sync->setSyncStatus(1);
            $sync->setPurchaseOrderNumber($purchaseOrderNumber);
            $sync->save();
            return true;
        } else {
            $sync->setSyncStatus(2);
            $sync->save();
            return true;
        }
    }

    /**
     * Get Order Data
     *
     * @param array $invoice
     * @return array
     */
    public function getOrderedItemData($invoice)
    {
        $orderItemData = [];
        $orderId = $invoice['entity_id'];
        $orderFactory = $this->orderfactory;
        $order = $orderFactory->create()->load($orderId);
        $items = $order->getAllVisibleItems();
        foreach ($items as $item) {
            $orderItem = [];
            $orderItem['article_number'] = str_replace([' ', '-'], '', $item->getSku());
            $orderItem['description'] = $item->getName();
            $orderItem['purchase_order_quantity'] = $item->getQtyOrdered();
            $orderItem['price'] = $item->getPriceInclTax();
            $orderItemData[] = $orderItem; // Append the item data to the array
        }
        return $orderItemData;
    }

    /**
     * Get WSDK Url
     *
     * @return string
     */
    public function getWsdlUrl()
    {
        return $this->config->getWsdlUrl();
    }

    /**
     * Get Request Data
     *
     * @param array $invoiceData
     * @param array $addressData
     * @param array $shippingAddressData
     * @return xml formate
     */
    public function buildInvoiceXml(array $invoiceData, array $addressData, array $shippingAddressData): string
    {
        $orderId = $invoiceData['entity_id'];
        $payment = $this->orderrepositoryinterface->get($orderId)->getPayment();
        $lastTransId = $payment->getLastTransId();
        $lastTransIdString = '';
        if (!empty($lastTransId)) {
            $lastTransIdString = "PID=\"$lastTransId\"";
        }
        $paymentMethod = $payment->getMethod();
        if ($paymentMethod == 'payone_paypal') {
            $paymentMethod = 'L';
        } elseif ($paymentMethod == 'payone_creditcard') {
            $paymentMethod = 'M';
        } else {
            $paymentMethod = '1';
        }
        $amount = isset($invoiceData['grand_total']) ? number_format($invoiceData['grand_total'], 2, '.', '') : '';
        $shippingCost = isset($invoiceData['shipping_amount']) ?
        number_format((float)$invoiceData['shipping_amount'], 2, '.', '') : '';
        $customerType = '';
        $customerTypeShip = '';

        if ($addressData['country_code'] == 'D') {
            $customerType = 'P1';
        } else {
            $customerType = 'P2';
        }

        if (array_key_exists('country_code', $shippingAddressData) && $shippingAddressData['country_code'] == 'D') {
            $customerTypeShip = 'P1';
        } else {
            $customerTypeShip = 'P2';
        }
        
        $shippingCostCode = '';
        if ($shippingCost >= 0.00) {
            $shippingCostCode = 'J';
        } else {
            $shippingCostCode = 'V';
        }
        $purchaseDate = isset($invoiceData['created_at']) ? date('Ymd', strtotime($invoiceData['created_at'])) : '';
        $orderItemData = $this->getOrderedItemData($invoiceData);
        $addressType = !empty($shippingAddressData) ? 'R' : 'L';
        $xmlString = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:vas="vasws.GPAuftrag">';
        $xmlString .= '<soap:Header/>';
        $xmlString .= '<soap:Body>';
        $xmlString .= '<vas:insertOrder>';
        $xmlString .= '<vas:xmlDaten><![CDATA[';
        // Now append the message XML
        $xmlString .= '<message xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                        xsi:noNamespaceSchemaLocation="order.xsd">';
        $xmlString .= '<header>';
        $xmlString .= '<client>000</client>';
        $xmlString .= '</header>';
        $xmlString .= '<body>';
        $xmlString .= '<purchase_order>';
        $xmlString .= '<purchase_order_id>' . 'VLG_' . $this->config->getPublisherId() . '_' . $invoiceData['entity_id'] . '</purchase_order_id>';
        $xmlString .= '<date_of_purchase_order>' . $purchaseDate . '</date_of_purchase_order>';
        $xmlString .= '<correspondence_language_code>' . 'DEU' . '</correspondence_language_code>';
        $xmlString .= '<payment_type>' . $paymentMethod . '</payment_type>';
        if ($paymentMethod != '1') {
            $xmlString .= '<payment_status>' . 'A' . '</payment_status>';
        }
        $xmlString .= '<payment_amount>' . $amount . '</payment_amount>';
        $xmlString .= '<shipping_cost_code>' . $shippingCostCode . '</shipping_cost_code>';
        $xmlString .= '<shipping_cost>' . $shippingCost . '</shipping_cost>';
        $xmlString .= '<order_type>' . 101 . '</order_type>';

        if (!$invoiceData['is_virtual']) {
            $xmlString .= '<address type="R">';
            $xmlString .= '<name1>' . $addressData['name1'] . '</name1>';
            $xmlString .= '<first_name>' . $addressData['first_name'] . '</first_name>';
            $xmlString .= '<street>' . $addressData['street'] . '</street>';
            $xmlString .= '<zipcode>' . $addressData['zipcode'] . '</zipcode>';
            $xmlString .= '<country_code>' . $addressData['country_code'] . '</country_code>';
            $xmlString .= '<city>' . $addressData['city'] . '</city>';
            $xmlString .= '<customer_type>' . $customerType . '</customer_type>';
            $xmlString .= '</address>';

            $xmlString .= '<address type="L">';
            $xmlString .= '<name1>' . $shippingAddressData['name1'] . '</name1>';
            $xmlString .= '<first_name>' . $shippingAddressData['first_name'] . '</first_name>';
            $xmlString .= '<street>' . $shippingAddressData['street'] . '</street>';
            $xmlString .= '<zipcode>' . $shippingAddressData['zipcode'] . '</zipcode>';
            $xmlString .= '<country_code>' . $shippingAddressData['country_code'] . '</country_code>';
            $xmlString .= '<city>' . $shippingAddressData['city'] . '</city>';
            $xmlString .= '<customer_type>' . $customerTypeShip . '</customer_type>';
            $xmlString .= '</address>';
        } else {
            $xmlString .= '<address type="R">';
            $xmlString .= '<name1>' . $addressData['name1'] . '</name1>';
            $xmlString .= '<first_name>' . $addressData['first_name'] . '</first_name>';
            $xmlString .= '<street>' . $addressData['street'] . '</street>';
            $xmlString .= '<zipcode>' . $addressData['zipcode'] . '</zipcode>';
            $xmlString .= '<country_code>' . $addressData['country_code'] . '</country_code>';
            $xmlString .= '<city>' . $addressData['city'] . '</city>';
            $xmlString .= '<customer_type>' . $customerType . '</customer_type>';
            $xmlString .= '</address>';
        }
        foreach ($orderItemData as $orderItem) {
            $xmlString .= '<item>';
            $xmlString .= '<article_number>' . $orderItem['article_number'] . '</article_number>';
            $xmlString .= '<description>' . $orderItem['description'] . '</description>';
            // @codingStandardsIgnoreStart
            $xmlString .= '<purchase_order_quantity>' . intval($orderItem['purchase_order_quantity']) . '</purchase_order_quantity>';
            // @codingStandardsIgnoreEnd
            $xmlString .= '<price>' . $orderItem['price'] . '</price>';
            $xmlString .= '</item>';
        }
        $xmlString .= '<stock_reserve>' . $this->config->getStockReserveValue() . '</stock_reserve>';
        $xmlString .= '<publisher_number>' . $this->config->getPublisherId() . '</publisher_number>';
        if ($paymentMethod != '1') {
            $xmlString .= '<cc_transaction_number>' . $lastTransIdString .'</cc_transaction_number>';
        }
        $xmlString .= '<source>' . $this->config->getOrderSource() . '</source>';
        $xmlString .= '<shipping_method_remainder>'.'1'.'</shipping_method_remainder>';
        $xmlString .= '<language_code>' . 'DEU' . '</language_code>';
        $xmlString .= '<merge_orders_into_one>' . 'A' . '</merge_orders_into_one>';
        $xmlString .= '</purchase_order>';
        $xmlString .= '</body>';
        $xmlString .= '</message>';
        // Close the CDATA and the SOAP elements
        $xmlString .= ']]></vas:xmlDaten>';
        $xmlString .= '<vas:ziel>EVA1</vas:ziel>';
        $xmlString .= '</vas:insertOrder>';
        $xmlString .= '</soap:Body>';
        $xmlString .= '</soap:Envelope>';
        return $xmlString;
    }

    /**
     * Get Address Data
     *
     * @param array $address
     * @return array
     */
    private function getAddress(?OrderAddressInterface $address): array
    {   
        if (!$address) {
            return [];
        }
        $streetLines = $address->getStreet();
        $street = implode(', ', $streetLines);
        $shortCountryCode = $this->shortCountryCode->getCountryValue($address->getCountryId());
        return [
            'name1' => $address->getLastname(),
            'first_name' => $address->getFirstname(),
            'street' => $street,
            'zipcode' => $address->getPostcode(),
            'country_code' => $shortCountryCode,
            'city' => $address->getCity(),
            'customer_type' => 'P1'
        ];
    }
}
