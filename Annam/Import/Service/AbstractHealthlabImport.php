<?php
declare(strict_types=1);
namespace Annam\Import\Service;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\SerializerInterface;

abstract class AbstractHealthlabImport
{
    /**
     * @var ResourceConnection
     */
    protected ResourceConnection $resource;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @param ResourceConnection $resource
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ResourceConnection $resource,
        SerializerInterface $serializer
    )
    {
        $this->resource = $resource;
        $this->serializer = $serializer;
    }
}
