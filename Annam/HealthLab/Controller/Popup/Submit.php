<?php
declare(strict_types=1);
namespace Annam\HealthLab\Controller\Popup;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Controller\ResultInterface;

class Submit extends Action
{
    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var CustomerFactory
     */
    protected CustomerFactory $customerFactory;

    /**
     * @var AccountManagementInterface
     */
    protected AccountManagementInterface $accountManagement;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Session $customerSession
     * @param CustomerFactory $customerFactory
     * @param AccountManagementInterface $accountManagement
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Session $customerSession,
        CustomerFactory $customerFactory,
        AccountManagementInterface $accountManagement
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->accountManagement = $accountManagement;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (isset($data['username']) && isset($data['password'])) {
            $username = $data['username'];
            $password = $data['password'];

            try {
                $customer = $this->accountManagement->authenticate($username, $password);
                $this->customerSession->setCustomerDataAsLoggedIn($customer);
                $this->customerSession->regenerateId();

                return $result->setData(['success' => true, 'message' => 'Login successful']);
            } catch (\Exception $e) {
                return $result->setData(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        return $result->setData(['success' => false, 'message' => 'Invalid login details']);
    }
}
