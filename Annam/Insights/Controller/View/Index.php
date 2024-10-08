<?php
declare(strict_types = 1);
namespace Annam\Insights\Controller\View;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Annam\HealthLab\Helper\Data as AnnamHelper;
use Magento\Framework\Serialize\SerializerInterface;
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected PageFactory $_pageFactory;

    protected SerializerInterface $serializer;

    /**
     * @var AnnamHelper
     */
    public AnnamHelper $annamHelper;

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
        AnnamHelper $annamHelper,
        SerializerInterface $serializer
    )
    {
        $this->serializer = $serializer;
        $this->annamHelper = $annamHelper;
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

        $breadcrumb = $this->sessionManager->getData('breadcrumb') ?? "[]";
        $breadcrumb = $this->serializer->unserialize($breadcrumb);

        $breadcrumb["insights"] = [
            "title" => $this->annamHelper->insightTitle(),
            "url" => $request->getRequestUri()
        ];

        $this->sessionManager->setData('breadcrumb' , $this->serializer->serialize($breadcrumb));

        // $logger->info($this->sessionManager->getData('breadcrumb'));

        return $this->_pageFactory->create();
    }
}
