<?php
declare(strict_types=1);
namespace TTTech\Import\Cron;

use TTTech\Import\Service\DeleteOldRecords;
use TTTech\Import\Helper\Data as HelperData;
use TTTech\Import\Model\ImportProduct;

class ImportCron
{
    /**
     * Summary of deleteOldRecords
     * @var DeleteOldRecords
     */
    protected DeleteOldRecords $deleteOldRecords;

    /**
     * Summary of helperData
     * @var HelperData
     */
    protected HelperData $helperData;

    /**
     * Summary of importProduct
     * @var ImportProduct
     */
    protected ImportProduct $importProduct;

    /**
     * Summary of __construct
     * @param \TTTech\Import\Helper\Data $helperData
     * @param \TTTech\Import\Service\DeleteOldRecords $deleteOldRecords
     * @param \TTTech\Import\Model\ImportProduct $importProduct
     */
    public function __construct
    (
        HelperData $helperData,
        DeleteOldRecords $deleteOldRecords,
        ImportProduct $importProduct
    )
    {
        $this->helperData = $helperData;
        $this->deleteOldRecords = $deleteOldRecords;
        $this->importProduct = $importProduct;
    }

    /**
     * Summary of execute
     * @return void
     */
    public function execute()
    {
        if((int)$this->helperData->statusModule()){
            $this->deleteOldRecords->handlerDeleteOldRecords();
            $this->importProduct->import();
        }
    }
}
