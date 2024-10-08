<?php
declare(strict_types=1);
namespace Annam\HealthLab\Controller\Wishlist;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory as WishlistCollectionFactory;

class Count extends Action
{
    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var WishlistCollectionFactory
     */
    protected WishlistCollectionFactory $wishlistCollectionFactory;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param WishlistCollectionFactory $wishlistCollectionFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        WishlistCollectionFactory $wishlistCollectionFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->customerSession = $customerSession;
        $this->wishlistCollectionFactory = $wishlistCollectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $customerId = $this->customerSession->getCustomerId();
        $wishlistCount = 0;

        if ($customerId) {
            $wishlistCollection = $this->wishlistCollectionFactory->create()->addCustomerIdFilter($customerId);
            $wishlistCount = $wishlistCollection->getSize();
        }

        $resultJson = $this->resultJsonFactory->create();
        $response = ['success' => true, 'message' => __('Done'), 'count' => $wishlistCount];
        return $resultJson->setData($response);
    }
}
