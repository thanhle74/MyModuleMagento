<?php
declare(strict_types=1);
namespace Annam\Import\Service;

use Annam\Dish\Model\ResourceModel\Dish\CollectionFactory;
use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\SerializerInterface;

class ImportDish extends AbstractHealthlabImport
{
     /**
     * @var ResourceConnection
     */
    protected ResourceConnection $resource;

    protected SerializerInterface $serializer;

    protected CollectionFactory $collectionFactory;

    public function __construct(
        ResourceConnection $resource,
        SerializerInterface $serializer,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($resource, $serializer);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param array $importData
     * @return void
     */
    public function handle(array $importData)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/import-dish.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('healthlab_dish');
        $logger->info("Start Import...");
        foreach ($importData as $row) {
            try {
                $data = [
                    'id' => $row[1],
                    'name' => $row[2],
                    'short_content' => $this->serializer->serialize($row[3]),
                    'long_content' => $this->serializer->serialize($row[4]),
                    'status' => $row[5],
                    'image' => $this->serializer->serialize($row[6]),
                    'video' => $row[7],
                    'products' => $row[8],
                ];

                $isExistRecord = $this->isEmpty($row[1]);

                if($isExistRecord){
                    $connection->insert($tableName, $data);

                } else {
                    $where = 'id='.$data['id'];
                    unset($data['id']);
                    $connection->update($tableName, $data, $where);
                }
            } catch (\Exception $e) {
                $logger->info($e->getMessage());
            }
        }
        $logger->info("Import success!");
    }

    /**
     * @return bool
     */
    private function isEmpty($id): bool
    {
        if(!isset($id) || $id == "" || $id == null) return true;

        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter('id', ['eq' => $id]);

        if($collection->count()){
            return false;
        } else {
            return true;
        }

        return false;
    }
}
