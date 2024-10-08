<?php
declare(strict_types=1);
namespace Annam\HealthLab\Controller\Wishlist;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory as WishlistCollectionFactory;

class Remove extends Action
{
    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var WishlistFactory
     */
    protected WishlistFactory $wishlistFactory;

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
     * @param WishlistFactory $wishlistFactory
     * @param WishlistCollectionFactory $wishlistCollectionFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        WishlistFactory $wishlistFactory,
        WishlistCollectionFactory $wishlistCollectionFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->customerSession = $customerSession;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistCollectionFactory = $wishlistCollectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $customerId = $this->customerSession->getCustomerId();
        $itemId = (int) $this->getRequest()->getParam('item_id');

        if ($customerId && $itemId) {
            try {
                $wishlist = $this->wishlistFactory->create()->loadByCustomerId($customerId, true);
                $item = $wishlist->getItem($itemId);

                if ($item) {
                    $item->delete();
                    $wishlist->save();
                    $response = ['success' => true, 'message' => __('Item removed from wishlist.')];
                } else {
                    $response = ['success' => false, 'message' => __('Item does not exist.')];
                }
            } catch (\Exception $e) {
                $response = ['success' => false, 'message' => $e->getMessage()];
            }
        } else {
            $response = ['success' => false, 'message' => __('Invalid request.')];
        }

        return $resultJson->setData($response);
    }
}
