<?php
declare(strict_types=1);
namespace Annam\Import\Service;

class ImportIngredient extends AbstractHealthlabImport
{
    /**
     * @param array $importData
     * @return void
     */
    public function handle(array $importData)
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('healthlab_mapping_url');

        foreach ($importData as $row) {
            if (isset($row[0]) && isset($row[1])) {
                $data = [
                    'id' => $row[0],
                    'url_vn' => $row[1],
                    'url_en' => $row[2],
                    'status' => (int)$row[3]
                ];

                if($this->isEmpty())
                {
                    $connection->insert($tableName, $data);
                }else
                {
                    $connection->update($tableName, $data);
                }
            }
        }
    }

    /**
     * @return bool
     */
    private function isEmpty(): bool
    {
        //Here logic
        return true;
    }
}
