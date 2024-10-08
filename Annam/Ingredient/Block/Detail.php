<?php
declare(strict_types=1);
namespace Annam\Ingredient\Block;

use Annam\HealthLab\Helper\Data as AnnamHelper;
use Annam\Ingredient\Model\ResourceModel\Ingredient\Collection as IngredientCollection;
use Annam\Ingredient\Model\ResourceModel\Ingredient\CollectionFactory as IngredientCollectionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;
use Annam\HealthLab\Model\ProductHelper;
use Magento\Framework\Session\SessionManagerInterface;

class Detail extends Template
{
    /**
     * @var AnnamHelper
     */
    public AnnamHelper $annamHelper;

    /**
     * @var SerializerInterface
     */
    public SerializerInterface $serializer;

    /**
     * @var IngredientCollectionFactory
     */
    public IngredientCollectionFactory $ingredientCollection;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var ProductHelper
     */
    protected ProductHelper $productHelper;

    protected SessionManagerInterface $sessionManager;

    /**
     * @param Context $context
     * @param IngredientCollectionFactory $ingredientCollection
     * @param SerializerInterface $serializer
     * @param AnnamHelper $annamHelper
     * @param ProductHelper $productHelper
     * @param ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        IngredientCollectionFactory $ingredientCollection,
        SerializerInterface $serializer,
        AnnamHelper $annamHelper,
        ProductHelper $productHelper,
        ProductRepositoryInterface $productRepository,
        SessionManagerInterface $sessionManager,
        array $data = []
    )
    {
        $this->sessionManager = $sessionManager;
        $this->annamHelper = $annamHelper;
        $this->ingredientCollection = $ingredientCollection;
        $this->serializer = $serializer;
        $this->productRepository = $productRepository;
        $this->productHelper = $productHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return int
     */
    public function getParamId(): int
    {
        return (int) $this->annamHelper->getParameterValue('id');
    }

    /**
     * @param int $id
     * @return IngredientCollection
     */
    public function getIngredientById(int $id): IngredientCollection
    {
        $ingredientCollection = $this->ingredientCollection->create();
        $ingredientCollection->addFieldToFilter('status', ['eq' => 1]);
        $ingredientCollection->addFieldToFilter('id', ['eq' => $id]);

        return $ingredientCollection;
    }

    /**
     * @return SerializerInterface
     */
    public function serializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @param string $sku
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    public function getProductBySku(string $sku): ProductInterface
    {
        return $this->productRepository->get($sku);
    }

    /**
     * @param $product
     * @return string
     */
    public function getUrlImage($product): string
    {
        return $this->productHelper->getUrlImage($product);
    }

    public function getBreadcrumb()
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);

        $this->sessionManager->start();
        $breadcrumb = $this->sessionManager->getData('breadcrumb') ?? "[]";
        $breadcrumb = $this->serializer->unserialize($breadcrumb);

        $data = [
            "0" => $breadcrumb["insights"] ?? null,
            "1" => $breadcrumb["insight_detail"] ?? null,
            "2" => $breadcrumb["ingredients_detail"] ?? null
        ];

        return $data;
    }
}
