<?php
declare(strict_types = 1);
namespace Annam\Ingredients\Controller\Detail;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Session\SessionManagerInterface;
use Annam\Ingredients\Model\ResourceModel\Ingredients\CollectionFactory as IngredientsCollectionFactory;
use Magento\Framework\Serialize\SerializerInterface;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected PageFactory $_pageFactory;

    protected IngredientsCollectionFactory $ingredientsCollectionFactory;

    protected SessionManagerInterface $sessionManager;

    protected SerializerInterface $serializer;

    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        SessionManagerInterface $sessionManager,
        IngredientsCollectionFactory $ingredientsCollectionFactory,
        SerializerInterface $serializer
    )
    {
        $this->serializer = $serializer;
        $this->ingredientsCollectionFactory =  $ingredientsCollectionFactory;
        $this->sessionManager = $sessionManager;
        $this->_pageFactory = $pageFactory;
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
        // $request->getParam('id')

        $ingredientsCollection = $this->ingredientsCollectionFactory->create();
        $ingredientsCollection->addFieldToFilter('status', ['eq' => 1]);
        $ingredientsCollection->addFieldToFilter('id', ['eq' => $request->getParam('id')]);

        $breadcrumb = $this->sessionManager->getData('breadcrumb') ?? "[]";
        $breadcrumb = $this->serializer->unserialize($breadcrumb);

        $title = "";
        foreach ($ingredientsCollection as $ingredients){
            $title = $ingredients->getName();
        }

        $breadcrumb["ingredients_detail"] = [
            "title" => $title,
            "url" => $request->getRequestUri()
        ];

        $this->sessionManager->setData('breadcrumb' , $this->serializer->serialize($breadcrumb));

        // $logger->info($this->sessionManager->getData('breadcrumb'));

        return $this->_pageFactory->create();
    }
}
