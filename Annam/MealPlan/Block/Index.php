<?php
declare(strict_types=1);
namespace Annam\MealPlan\Block;

use Annam\MealPlan\Model\ResourceModel\Meal\Collection;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Psr\Log\LoggerInterface;
use Annam\MealPlan\Model\ResourceModel\Meal\CollectionFactory as MealCollectionFactory;
use Magento\Framework\Serialize\SerializerInterface;

class Index extends Template
{
    /**
     * @var MealCollectionFactory
     */
    public MealCollectionFactory $mealCollectionFactory;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @param Context $context
     * @param LoggerInterface $logger
     * @param MealCollectionFactory $mealCollectionFactory
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        LoggerInterface $logger,
        MealCollectionFactory $mealCollectionFactory,
        SerializerInterface $serializer,
        array $data = []
    )
    {
        $this->logger = $logger;
        $this->mealCollectionFactory = $mealCollectionFactory;
        $this->serializer = $serializer;
        parent::__construct($context, $data);
    }

    /**
     * @return Collection
     */
    public function getMeals(): Collection
    {
        $mealCollection = $this->mealCollectionFactory->create();
        $mealCollection->addFieldToFilter('status', 1);

        return $mealCollection;
    }

    /**
     * @param string $img
     * @return array|bool|float|int|string|null
     */
    public function getImg(string $img)
    {
        return $this->serializer->unserialize($img);
    }

    /**
     * @return SerializerInterface
     */
    public function serializer(): SerializerInterface
    {
        return $this->serializer;
    }
}
