<?php
declare(strict_types=1);
namespace Annam\HealthLab\Controller\Wishlist;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Add extends Action
{
    /**
     * @var WishlistProviderInterface
     */
    protected WishlistProviderInterface $wishlistProvider;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @param Context $context
     * @param WishlistProviderInterface $wishlistProvider
     * @param ProductRepositoryInterface $productRepository
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        WishlistProviderInterface $wishlistProvider,
        ProductRepositoryInterface $productRepository,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->wishlistProvider = $wishlistProvider;
        $this->productRepository = $productRepository;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $productId = (int) $this->getRequest()->getParam('product');
        try {
            $wishlist = $this->wishlistProvider->getWishlist();
            $product = $this->productRepository->getById($productId);

            $wishlist->addNewItem($product);
            $wishlist->save();

            $response = ['success' => true, 'message' => __('Product added to wishlist.')];
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => __('Error adding product to wishlist.')];
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
