<?php
declare(strict_types=1);
namespace Annam\Import\Service;

class ImportMealPlan extends AbstractHealthlabImport
{
    /**
     * @param array $importData
     * @return void
     */
    public function handle(array $importData)
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('healthlab_meal_plan');

        foreach ($importData as $row) {
            if (isset($row[0]) && isset($row[1])) {
                $data = [
                    'id' => $row[0],
                    'name' => $row[1],
                    'image' => $row[2],
                    'content' => (int)$row[3],
                    'store' => $row[4],
                    'day_1' => $row[5],
                    'day_2' => $row[6],
                    'day_3' => $row[7],
                    'day_4' => $row[8],
                    'day_5' => $row[9],
                    'day_6' => $row[10],
                    'day_7' => $row[11],
                    'status' => $row[12],
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
