<?php
declare(strict_types=1);
namespace Annam\Insights\Block;

use Annam\Insights\Model\ResourceModel\Insights\Collection;
use Annam\Ingredients\Model\ResourceModel\Ingredients\Collection as IngredientsCollection;
use Annam\HealthLab\Helper\Data as AnnamHelper;
use Annam\Insights\Model\ResourceModel\Insights\CollectionFactory as InsightsCollectionFactory;
use Annam\Ingredients\Model\ResourceModel\Ingredients\CollectionFactory as IngredientsCollectionFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;

class Detail extends AbstractInsights
{
    /**
     * @var AnnamHelper
     */
    public AnnamHelper $annamHelper;

    /**
     * @var IngredientsCollectionFactory
     */
    protected IngredientsCollectionFactory $ingredientsCollection;

    /**
     * @param Context $context
     * @param InsightsCollectionFactory $insightsCollection
     * @param IngredientsCollectionFactory $ingredientsCollection
     * @param SerializerInterface $serializer
     * @param AnnamHelper $annamHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        InsightsCollectionFactory $insightsCollection,
        IngredientsCollectionFactory $ingredientsCollection,
        SerializerInterface $serializer,
        AnnamHelper $annamHelper,
        array $data = []
    )
    {
        $this->annamHelper = $annamHelper;
        $this->ingredientsCollection = $ingredientsCollection;
        parent::__construct($context, $insightsCollection, $serializer, $data);
    }

    /**
     * @param int $id
     * @return Collection
     */
    public function getInsightsById(int $id): Collection
    {
        $insightsCollection = $this->insightsCollection->create();
        $insightsCollection->addFieldToFilter('status', ['eq' => 1]);
        $insightsCollection->addFieldToFilter('id', ['eq' => $id]);

        return $insightsCollection;
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
     * @return IngredientsCollection
     */
    public function getIngredientsById(int $id): IngredientsCollection
    {
        $ingredientsCollection = $this->ingredientsCollection->create();
        $ingredientsCollection->addFieldToFilter('status', ['eq' => 1]);
        $ingredientsCollection->addFieldToFilter('id', ['eq' => $id]);

        return $ingredientsCollection;
    }
}
