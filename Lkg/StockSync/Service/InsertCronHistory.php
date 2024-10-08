<?php
declare(strict_types=1);

namespace Lkg\StockSync\Service;

use Exception;
use Lkg\Base\Api\CronHistoryRepositoryInterface;
use Lkg\Base\Helper\Data as HelperBaseLkg;
use Lkg\Base\Model\CronHistoryFactory;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory as ScheduleCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use DateTime;

class InsertCronHistory
{
    /**
     * @var CronHistoryFactory
     */
    protected CronHistoryFactory $cronHistoryFactory;

    /**
     * @var CronHistoryRepositoryInterface
     */
    protected CronHistoryRepositoryInterface $cronHistoryRepository;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var HelperBaseLkg
     */
    protected HelperBaseLkg $helperBaseLkg;

    /**
     * @var ScheduleCollectionFactory
     */
    protected ScheduleCollectionFactory $scheduleCollectionFactory;

    /**
     * @param CronHistoryFactory $cronHistoryFactory
     * @param CronHistoryRepositoryInterface $cronHistoryRepository
     * @param LoggerInterface $logger
     * @param HelperBaseLkg $helperBaseLkg
     * @param ScheduleCollectionFactory $scheduleCollectionFactory
     */
    public function __construct
    (
        CronHistoryFactory $cronHistoryFactory,
        CronHistoryRepositoryInterface $cronHistoryRepository,
        LoggerInterface $logger,
        HelperBaseLkg $helperBaseLkg,
        ScheduleCollectionFactory $scheduleCollectionFactory
    )
    {
        $this->cronHistoryFactory = $cronHistoryFactory;
        $this->cronHistoryRepository = $cronHistoryRepository;
        $this->logger = $logger;
        $this->helperBaseLkg = $helperBaseLkg;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
    }

    /**
     * @param array $params
     * @return void
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function handlerInsertCronHistory(array $params): void
    {
        if((int)$this->helperBaseLkg->isStatusHistoryCron())
        {
            $cronHistory = $this->cronHistoryFactory->create();
            $cronHistory->setJobCode($params['job_code']);
            $cronHistory->setPublisherId($params['request']['verlag']);
            $cronHistory->setRequest(
                $this->soapRequestResult($params['request']['verlag'], $params['request']['ziel'])
            );

            $cronHistory->setResponse(
                $this->soapResponseResult($params['response'])
            );

            $cronHistory->setStatus('pending');

            $executedAt = new DateTime($this->getSpecificCronRecord($params['job_code'])->getExecutedAt());
            $cronHistory->setExecutedAt($executedAt);
            try {
                $this->cronHistoryRepository->save($cronHistory);
            }catch (Exception $e){
                $this->logger->error($e->getMessage());
            }
        }
    }

    /**
     * @param string $jobCode
     * @return mixed
     */
    private function getSpecificCronRecord(string $jobCode)
    {
        $collection = $this->scheduleCollectionFactory->create();
        $collection->addFieldToFilter('job_code', $jobCode);
        $collection->setOrder('executed_at', 'DESC');

        return $collection->getFirstItem();
    }

    /**
     * @param string $verlag
     * @param string $ziel
     * @return string
     */
    private function soapRequestResult(string $verlag, string $ziel): string
    {
        return sprintf(
        '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:vas="vas.Artikel">
                   <soap:Header/>
                   <soap:Body>
                      <vas:getStockInfoListPublisher>
                         <vas:verlag>%s</vas:verlag>
                         <vas:ziel>%s</vas:ziel>
                      </vas:getStockInfoListPublisher>
                   </soap:Body>
                </soap:Envelope>', $verlag, $ziel);
    }


    /**
     * @param string $stockItems
     * @return string
     */
    private function soapResponseResult(string $stockItems): string
    {
        return sprintf(
        '<soapenv:Envelope xmlns:soapenv="http://www.w3.org/2003/05/soap-envelope">
                   <soapenv:Header/>
                   <soapenv:Body>
                      <ns:getStockInfoListPublisherResponse xmlns:ns="vas.Artikel">
                         <ns:return><![CDATA[<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                <message>
                    %s
                </message>]]></ns:return>
                      </ns:getStockInfoListPublisherResponse>
                   </soapenv:Body>
                </soapenv:Envelope>', $stockItems);
    }
}
