<?php
declare(strict_types=1);
namespace Lkg\StockSync\Service;

use Lkg\Base\Service\AbstractHandlerSync;
use Magento\Framework\Exception\NoSuchEntityException;
use SoapClient;
use SoapFault;
use Psr\Log\LoggerInterface;
use Lkg\StockSync\Helper\Data as StockSyncHelper;
use SimpleXMLElement;
use Lkg\Base\Helper\Data as BaseSyncHelper;

class HandlerStockSync extends AbstractHandlerSync
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var StockSyncHelper
     */
    private StockSyncHelper $stockSyncHelper;

    /**
     * @var BaseSyncHelper
     */
    private BaseSyncHelper $baseSyncHelper;

    /**
     * @param LoggerInterface $logger
     * @param StockSyncHelper $stockSyncHelper
     * @param BaseSyncHelper $baseSyncHelper
     */
    public function __construct
    (
        LoggerInterface $logger,
        StockSyncHelper $stockSyncHelper,
        BaseSyncHelper $baseSyncHelper
    )
    {
        $this->logger = $logger;
        $this->stockSyncHelper = $stockSyncHelper;
        $this->baseSyncHelper = $baseSyncHelper;
    }

    /**
     * @param array $params
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function fetchInfo(array $params): mixed
    {
        try {
            $client = new SoapClient(
                $this->stockSyncHelper->wsdlUrl(),
                $this->getSoapOptions(
                    [
                        'login' => $this->baseSyncHelper->soapUser(),
                        'password' => $this->baseSyncHelper->soapPassword()
                    ]
                )
            );

            return $client->getStockInfoListPublisher($params);

        } catch (SoapFault $e) {
            $this->logger->error(__('SOAP Error: ') . $e->getMessage());
            return null;
        }
    }

    /**
     * @param $response
     * @return SimpleXMLElement|null
     */
    public function parseResponse($response): ?SimpleXMLElement
    {
        if (empty($response)) {
            return null;
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response->return);

        if ($xml === false)
        {
            $this->logger->error(__('XML Parsing Error'));
            return null;
        }

        return $xml->stock_info;
    }
}
