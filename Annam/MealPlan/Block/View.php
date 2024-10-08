<?php
declare(strict_types=1);
namespace Annam\MealPlan\Block;

use Magento\Framework\View\Element\Template;
use Annam\HealthLab\Helper\Data as AnnamHelper;
use Psr\Log\LoggerInterface;
use Annam\MealPlan\Api\MealRepositoryInterface;
use Annam\Dish\Api\DishRepositoryInterface;
use Magento\Framework\Serialize\SerializerInterface;

class View extends Template
{
    /**
     * @var AnnamHelper
     */
    public AnnamHelper $annamHelper;

    /**
     * @var MealRepositoryInterface
     */
    public MealRepositoryInterface $mealRepository;

    /**
     * @var DishRepositoryInterface
     */
    public DishRepositoryInterface $dishRepository;

    /**
     * @var SerializerInterface
     */
    public SerializerInterface $serializer;

    /**
     * @param Template\Context $context
     * @param AnnamHelper $annamHelper
     * @param LoggerInterface $logger
     * @param MealRepositoryInterface $mealRepository
     * @param DishRepositoryInterface $dishRepository
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        AnnamHelper $annamHelper,
        LoggerInterface $logger,
        MealRepositoryInterface $mealRepository,
        DishRepositoryInterface $dishRepository,
        SerializerInterface $serializer,
        array $data = []
    )
    {
        $this->annamHelper = $annamHelper;
        $this->logger = $logger;
        $this->mealRepository = $mealRepository;
        $this->dishRepository = $dishRepository;
        $this->serializer = $serializer;
        parent::__construct($context, $data);
    }

    public function getMeal()
    {
        return $this->mealRepository->getById($this->getParamId());
    }

    public function getParamId(): int
    {
        return (int) $this->annamHelper->getParameterValue('id');
    }

    public function getDish(int $id)
    {
        return $this->dishRepository->getById($id);
    }

    public function convertJsonToArray(string $jsonString)
    {
        return $this->serializer->unserialize($jsonString);
    }
}
