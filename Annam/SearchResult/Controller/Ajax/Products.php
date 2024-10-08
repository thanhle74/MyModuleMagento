<?php
declare(strict_types=1);
namespace Annam\SearchResult\Controller\Ajax;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Annam\HealthLab\Model\ProductHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Annam\HealthLab\Helper\Data as AnnamHelper;

class Products extends Action
{
    const TEMPLATE  = 'Annam_SearchResult::products/get-list.phtml';
    const BLOCK     = 'Annam\HealthLab\Block\Product';

    /**
     * @var PageFactory
     */
    protected PageFactory $_resultPageFactory;

    /**
     * @var AnnamHelper
     */
    protected AnnamHelper $annamHelper;

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
     * @var ProductHelper
     */
    protected ProductHelper $productHelper;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $_resultJsonFactory
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param ProductHelper $productHelper
     * @param LoggerInterface $logger
     */
    public function __construct
    (
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $_resultJsonFactory,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        ProductHelper $productHelper,
        LoggerInterface $logger,
        AnnamHelper $annamHelper
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $_resultJsonFactory;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->productHelper = $productHelper;
        $this->logger = $logger;
        $this->annamHelper = $annamHelper;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $valueId     =   (int)$params['id'];
        $type   =   $params['type'];
        $keyword =  $params['keyword'];
        $sort   =   $params['sort'] == 'asc' ? DataCollection::SORT_ORDER_ASC : DataCollection::SORT_ORDER_DESC;
        $data   =   [];
        try {
            $attributeSetIdSortOrder = $this->sortOrderBuilder->setField($type)->setDirection($sort)->create();
            $attributeCode = $this->annamHelper->searchByKeyword();
            $this->searchCriteriaBuilder->addSortOrder($attributeSetIdSortOrder);

            $this->searchCriteriaBuilder->addFilter($attributeCode, $valueId);

            if($keyword != 'all')
            {
                $keyword = '%' . $keyword . '%';
                $this->searchCriteriaBuilder->addFilter('name', $keyword, 'like');
            }

            $this->searchCriteriaBuilder->addFilter('status' , Status::STATUS_ENABLED);
            $this->searchCriteriaBuilder->addFilter('visibility' , Visibility::VISIBILITY_NOT_VISIBLE, 'neq');
            $searchCriteria =$this->searchCriteriaBuilder->create();
            $productList = $this->productRepository->getList($searchCriteria);
            foreach ($productList->getItems() as $product) {
                if($product->getId())
                {
                    $data[]   =   [
                        'id'    =>  $product->getId(),
                        'name'  =>  $product->getname(),
                        'image' =>  $this->productHelper->getUrlImage($product),
                        'price' =>  $product->getPrice()
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }

        $result = $this->_resultJsonFactory->create();
        $resultPage = $this->_resultPageFactory->create();
        $block = $resultPage->getLayout()->createBlock(self::BLOCK)->setTemplate(self::TEMPLATE)->setData('data',$data)->toHtml();
        $result->setData(['output' => $block]);

        return $result;
    }
}
