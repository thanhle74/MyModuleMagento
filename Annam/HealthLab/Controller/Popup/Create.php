<?php
declare(strict_types=1);
namespace Annam\HealthLab\Controller\Popup;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;

class Create extends Action
{
    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var CustomerFactory
     */
    protected CustomerFactory $customerFactory;

    /**
     * @var AccountManagementInterface
     */
    protected AccountManagementInterface $accountManagement;

    /**
     * @var CustomerInterfaceFactory
     */
    protected CustomerInterfaceFactory $customerInterfaceFactory;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CustomerFactory $customerFactory
     * @param AccountManagementInterface $accountManagement
     * @param CustomerInterfaceFactory $customerInterfaceFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CustomerFactory $customerFactory,
        AccountManagementInterface $accountManagement,
        CustomerInterfaceFactory $customerInterfaceFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerFactory = $customerFactory;
        $this->accountManagement = $accountManagement;
        $this->customerInterfaceFactory = $customerInterfaceFactory;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (isset($data['email']) && isset($data['password']) && isset($data['firstname']) && isset($data['lastname']) && isset($data['dob'])) {
            try {
                /** @var CustomerInterface $customer */
                $customer = $this->customerInterfaceFactory->create();
                $customer->setWebsiteId(6);
                $customer->setDob($data['dob']);
                $customer->setEmail($data['email']);
                $customer->setFirstname($data['firstname']);
                $customer->setLastname($data['lastname']);

                $this->accountManagement->createAccount($customer, $data['password']);

                return $result->setData(['success' => true, 'message' => 'Registration successful']);
            } catch (LocalizedException $e) {
                return $result->setData(['success' => false, 'message' => $e->getMessage()]);
            } catch (\Exception $e) {
                return $result->setData(['success' => false, 'message' => 'An error occurred during registration']);
            }
        }

        return $result->setData(['success' => false, 'message' => 'Invalid registration details']);
    }
}
