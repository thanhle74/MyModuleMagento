<?php
declare(strict_types=1);
namespace Annam\MealPlan\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Annam\HealthLab\Helper\Data as AnnamHelper;
use Annam\Dish\Api\DishRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;

class Products extends Action
{
    const TEMPLATE = "Annam_MealPlan::products/get-list.phtml";
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
     * @var AnnamHelper
     */
    protected AnnamHelper $annamHelper;

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
     * @var DishRepositoryInterface
     */
    protected DishRepositoryInterface $dishRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $_resultJsonFactory
     * @param AnnamHelper $annamHelper
     * @param DishRepositoryInterface $dishRepository
     * @param ProductRepositoryInterface $productRepository
     * @param SortOrderBuilder $sortOrderBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $_resultJsonFactory,
        AnnamHelper $annamHelper,
        DishRepositoryInterface $dishRepository,
        ProductRepositoryInterface $productRepository,
        SortOrderBuilder $sortOrderBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $_resultJsonFactory;
        $this->annamHelper = $annamHelper;
        $this->dishRepository = $dishRepository;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $id     =   (int)$params['id'];
        $type   =   $params['type'];
        $keyword =  $params['keyword'];
        $valueOption = $params['valueOption'];
        $sort   =   $params['sort'] == 'asc' ? DataCollection::SORT_ORDER_ASC : DataCollection::SORT_ORDER_DESC;

        $valueOption = '%' . $valueOption . '%';
        $attributeCode = $this->annamHelper->mealPlanAttribute();
        $dish = $this->dishRepository->getById($id);
        $data = $dish->getData();
        $listSku = [];
        if(trim($data['products']))
        {
            foreach (explode("," ,$data['products']) as $product)
            {
                $listSku[] = trim($product);
            }
        }

        $sortOrder = $this->sortOrderBuilder->setField($type)->setDirection($sort)->create();
        if($keyword == 'all')
        {
            $this->searchCriteriaBuilder
                ->addFilter('sku', $listSku, 'in')
                ->addFilter($attributeCode, $valueOption , 'like')
                ->addSortOrder($sortOrder);
        }else{
            $keyword = '%' . $keyword . '%';
            $this->searchCriteriaBuilder
                ->addFilter('sku', $listSku, 'in')
                ->addFilter($attributeCode, $valueOption , 'like')
                ->addFilter('name' , $keyword , 'like')
                ->addSortOrder($sortOrder);
        }

        $this->searchCriteriaBuilder->addFilter('status' , Status::STATUS_ENABLED);
        $this->searchCriteriaBuilder->addFilter('visibility' , Visibility::VISIBILITY_NOT_VISIBLE, 'neq');
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $products = $this->productRepository->getList($searchCriteria)->getItems();

        $result = $this->_resultJsonFactory->create();
        $resultPage = $this->_resultPageFactory->create();
        $block = $resultPage->getLayout()->createBlock(self::BLOCK)->setTemplate(self::TEMPLATE)->setData('data',$products)->toHtml();
        $result->setData(['output' => $block]);

        return $result;
    }
}
