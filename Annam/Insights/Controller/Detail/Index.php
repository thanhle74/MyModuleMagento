<?php
declare(strict_types = 1);
namespace Annam\Insights\Controller\Detail;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Annam\Insights\Model\ResourceModel\Insights\CollectionFactory as InsightsCollectionFactory;
use Magento\Framework\Serialize\SerializerInterface;
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var AnnamHelper
     */
    public InsightsCollectionFactory $insightsCollection;

    /**
     * @var PageFactory
     */
    protected PageFactory $_pageFactory;

    protected SerializerInterface $serializer;

    /**
     * @var SessionManagerInterface
     */
    protected SessionManagerInterface $sessionManager;

    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param SessionManagerInterface $sessionManager
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        SessionManagerInterface $sessionManager,
        InsightsCollectionFactory $insightsCollection,
        SerializerInterface $serializer
    )
    {
        $this->serializer = $serializer;
        $this->insightsCollection = $insightsCollection;
        $this->_pageFactory = $pageFactory;
        $this->sessionManager = $sessionManager;
        return parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);

        $this->sessionManager->start();
        $request = $this->getRequest();

        $insightsCollection = $this->insightsCollection->create();
        $insightsCollection->addFieldToFilter('status', ['eq' => 1]);
        $insightsCollection->addFieldToFilter('id', ['eq' => $request->getParam('id')]);

        $breadcrumb = $this->sessionManager->getData('breadcrumb') ?? "[]";
        $breadcrumb = $this->serializer->unserialize($breadcrumb);

        $title = "";
        foreach ($insightsCollection as $insights){
            $title = $insights->getName();
        }

        $breadcrumb["insight_detail"] = [
            "title" => $title,
            "url" => $request->getRequestUri()
        ];

        $this->sessionManager->setData('breadcrumb' , $this->serializer->serialize($breadcrumb));

        // $logger->info($this->sessionManager->getData('breadcrumb'));

        return $this->_pageFactory->create();
    }
}
