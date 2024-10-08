<?php
declare(strict_types=1);
namespace Annam\HealthLab\Controller\Wishlist;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Customer\Model\Session;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory as WishlistCollectionFactory;

class Show extends Action
{
    const TEMPLATE = "Annam_HealthLab::wishlist/get-list.phtml";
    const BLOCK = "Annam\HealthLab\Block\Product";

    /**
     * @var PageFactory
     */
    protected PageFactory $_resultPageFactory;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $_resultJsonFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    protected SortOrderBuilder $sortOrderBuilder;

    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var WishlistCollectionFactory
     */
    protected WishlistCollectionFactory $wishlistCollectionFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $_resultJsonFactory
     * @param ProductRepositoryInterface $productRepository
     * @param SortOrderBuilder $sortOrderBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Session $customerSession
     * @param WishlistCollectionFactory $wishlistCollectionFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $_resultJsonFactory,
        ProductRepositoryInterface $productRepository,
        SortOrderBuilder $sortOrderBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Session $customerSession,
        WishlistCollectionFactory $wishlistCollectionFactory
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $_resultJsonFactory;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->customerSession = $customerSession;
        $this->wishlistCollectionFactory = $wishlistCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $type   =   $params['type'];
        $sort   =   $params['sort'] == 'asc' ? DataCollection::SORT_ORDER_ASC : DataCollection::SORT_ORDER_DESC;

        $customerId = $this->customerSession->getCustomerId();
        $listSku = [];
        $listItems = [];
        if ($customerId) {
            $wishlistCollection = $this->wishlistCollectionFactory->create()->addCustomerIdFilter($customerId);
            foreach ($wishlistCollection as $item) {
                $listSku[] = $item->getProduct()->getSku();
                $listItems[$item->getProduct()->getId()] = $item->getId();
            }
        }

        $sortOrder = $this->sortOrderBuilder->setField($type)->setDirection($sort)->create();
        $this->searchCriteriaBuilder->addFilter('sku', $listSku, 'in')->addSortOrder($sortOrder);
        $this->searchCriteriaBuilder->addFilter('status' , Status::STATUS_ENABLED);
        $this->searchCriteriaBuilder->addFilter('visibility' , Visibility::VISIBILITY_NOT_VISIBLE, 'neq');
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $products = $this->productRepository->getList($searchCriteria)->getItems();

        $result = $this->_resultJsonFactory->create();
        $resultPage = $this->_resultPageFactory->create();
        $block = $resultPage->getLayout()->createBlock(self::BLOCK)->setTemplate(self::TEMPLATE)->setData([
            'data'=> $products,
            'items' => $listItems,
        ])->toHtml();
        $result->setData(['output' => $block]);

        return $result;
    }
}
