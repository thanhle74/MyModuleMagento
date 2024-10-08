<?php
declare(strict_types=1);

namespace Lkg\StockSync\Controller\Adminhtml\StockSync;

use Lkg\StockSync\Service\PublisherStockSyncProcessor;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class Sync extends Action
{
    /**
     * @var PublisherStockSyncProcessor
     */
    protected PublisherStockSyncProcessor $publisherStockSyncProcessor;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param PublisherStockSyncProcessor $publisherStockSyncProcessor
     */
    public function __construct(
        Action\Context               $context,
        JsonFactory                  $resultJsonFactory,
        PublisherStockSyncProcessor $publisherStockSyncProcessor
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->publisherStockSyncProcessor = $publisherStockSyncProcessor;
        parent::__construct($context);
    }

    /**
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function execute()
    {
        $response = $this->resultJsonFactory->create();
        $this->publisherStockSyncProcessor->handlerPublisherStockSyncProcessor();
        $response->setData(["message" => ("Invalid Data"), "success" => true]);
        return $response;
    }
}
