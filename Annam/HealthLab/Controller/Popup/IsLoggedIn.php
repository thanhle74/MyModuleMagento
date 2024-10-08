<?php
declare(strict_types=1);
namespace Annam\HealthLab\Controller\Popup;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Serialize\SerializerInterface;

class IsLoggedIn extends Action
{
    /**
     * @var CustomerSession
     */
    protected CustomerSession $customerSession;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @param Context $context
     * @param SerializerInterface $serializer
     * @param JsonFactory $resultJsonFactory
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        SerializerInterface $serializer,
        JsonFactory $resultJsonFactory,
        CustomerSession $customerSession
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->serializer = $serializer;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        return $result->setData(['success' => $this->customerSession->isLoggedIn(), 'message' => '']);
    }
}
