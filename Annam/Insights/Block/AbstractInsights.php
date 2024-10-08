<?php
declare(strict_types=1);
namespace Annam\Insights\Block;

use Annam\Insights\Model\ResourceModel\Insights\Collection;
use Magento\Framework\View\Element\Template;
use Annam\Insights\Model\ResourceModel\Insights\CollectionFactory as InsightsCollectionFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template\Context;

abstract class AbstractInsights extends Template
{
    /**
     * @var InsightsCollectionFactory
     */
    protected InsightsCollectionFactory $insightsCollection;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @param Context $context
     * @param InsightsCollectionFactory $insightsCollection
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        InsightsCollectionFactory $insightsCollection,
        SerializerInterface $serializer,
        array $data = []
    )
    {
        $this->insightsCollection = $insightsCollection;
        $this->serializer = $serializer;
        parent::__construct($context, $data);
    }

    /**
     * @return Collection
     */
    public function getInsights(): Collection
    {
        $insightsCollection = $this->insightsCollection->create();
        $insightsCollection->addFieldToFilter('status', 1);

        return $insightsCollection;
    }

    /**
     * @return SerializerInterface
     */
    public function serializer(): SerializerInterface
    {
        return $this->serializer;
    }
}
