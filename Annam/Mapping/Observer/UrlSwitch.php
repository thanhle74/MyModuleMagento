<?php
declare(strict_types = 1);
namespace Annam\Mapping\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Store\Model\StoreManagerInterface;
use Annam\Mapping\Model\ResourceModel\Mapping\CollectionFactory;

class UrlSwitch implements ObserverInterface
{
    protected RedirectInterface $redirect;
    protected StoreManagerInterface $_storeManager;
    protected CollectionFactory $mappingCollection;

    public function __construct(
        RedirectInterface $redirect,
        StoreManagerInterface $storeManagerInterface,
        CollectionFactory $mappingCollection
    )
    {
        $this->redirect = $redirect;
        $this->_storeManager = $storeManagerInterface;
        $this->mappingCollection = $mappingCollection;
    }

    public function execute(Observer $observer)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);

        $controller = $observer->getControllerAction();
        $request = $controller->getRequest();

        $currentUrl = $request->getRequestUri();
        $currentUrl = trim($currentUrl, '/'); // remove "/" head && tail of url

        $parts = explode('/', $currentUrl);

        $offset = ( in_array('vi', $parts) || in_array('en', $parts)) ? 2 : 1; // default language EN

        $desiredParts = array_slice($parts, $offset); // ["vi", "insight","id","2"] -> ["insight","id","2"]

        $desiredPartsString = implode('/', $desiredParts); // ["insight","id","2"] -> insight/id/2

        $mappingCollection = $this->mappingCollection->create(); // create query select;

        $mappingCollection->addFieldToFilter( // find all record have same url
            ['url_vn', 'url_en'],
            [
                ['like' => '%' . $desiredPartsString . '%'],
                ['like' => '%' . $desiredPartsString . '%']
            ]
        );

        $results = $mappingCollection->getItems();

        if(count($results))
        {
            $firstItem = $mappingCollection->getFirstItem();

            $urlDirect = $firstItem->getUrlEn() ?? ""; // default language EN

            if(in_array('vi', $parts)) // is VN language config
                $urlDirect = $firstItem->getUrlVn() ?? "";

            if($desiredPartsString != $urlDirect && $urlDirect != ""){
                $this->redirect->redirect($controller->getResponse(), $urlDirect);
            }
        }
    }
}
