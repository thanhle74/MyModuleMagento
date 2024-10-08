<?php
declare(strict_types=1);
namespace Annam\Import\Service;
use Annam\Mapping\Model\ResourceModel\Mapping\CollectionFactory;
use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\SerializerInterface;

class ImportMapping extends AbstractHealthlabImport
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
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/import-mapping.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('healthlab_mapping_url');

        $logger->info("Start import Mapping...");
        $line = 1;
        foreach ($importData as $row) {
            try {
                if (isset($row[2]) && isset($row[3])) {
                    $data = [
                        'id' => $row[1],
                        'url_en' => trim($row[2], '/'),
                        'url_vn' => trim($row[3], '/'),
                        'status' => (int)$row[4]
                    ];

                    $isExistRecord = $this->isEmpty($row[1]);

                    if(!$this->isValid($data, $isExistRecord) ) {
                        throw new Exception("Exception : DataExisted row:".$line);
                    }

                    if($isExistRecord){
                        $connection->insert($tableName, $data);

                    } else {
                        $where = 'id='.$data['id'];
                        unset($data['id']);
                        $connection->update($tableName, $data, $where);
                    }
                } else {
                    throw new Exception("Exception : FieldRequired row:".$line);
                }
            } catch (\Exception $e) {
                $logger->info($e->getMessage());
            }
            $line++;
        }

        $logger->info("Import Mapping Successfully!");
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

    /**
     * @return bool
     */
    private function isValid($data, $isExistRecord): bool
    {
        $collection = $this->collectionFactory->create();

        if(! $isExistRecord){
            $collection->addFieldToFilter('id', ['neq' => $data["id"]]);
        }

        $collection->addFieldToFilter( // find all record have same url
            ['url_vn', 'url_en'],
            [
                ['like' => '%' . $data['url_vn'] . '%'],
                ['like' => '%' . $data['url_en'] . '%']
            ]
        );

        if($collection->count()){
            return false;
        }

        return true;
    }
}
