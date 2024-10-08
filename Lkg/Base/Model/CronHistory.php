<?php
declare(strict_types=1);
namespace Lkg\Base\Model;

use Magento\Framework\Model\AbstractModel;
use Lkg\Base\Api\Data\CronHistoryInterface;
use DateTime;
use Exception;

class CronHistory extends AbstractModel implements CronHistoryInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lkg\Base\Model\ResourceModel\CronHistory');
    }

    /**
     * @return array|mixed|null
     */
    public function getPublisherId()
    {
        return $this->getData(self::PUBLISHER_ID);
    }

    /**
     * @param $publisher_id
     * @return mixed
     */
    public function setPublisherId($publisher_id)
    {
        return $this->setData(self::PUBLISHER_ID, $publisher_id);
    }

    /**
     * @return DateTime|null
     * @throws \Exception
     */
    public function getExecutedAt(): ?DateTime
    {
        $executedAt = $this->getData(self::EXECUTED_AT);
        if ($executedAt) {
            try {
                return new DateTime($executedAt);
            } catch (Exception $e) {
                $this->_logger->error(__('DateTime parsing error: ') . $e->getMessage());
                return null;
            }
        }
        return null;
    }

    /**
     * @param DateTime $dateTime
     * @return CronHistory|mixed
     */
    public function setExecutedAt(DateTime $dateTime)
    {
        return $this->setData(self::EXECUTED_AT, $dateTime->format('Y-m-d H:i:s'));
    }

    /**
     * @return array|mixed|null
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @param $message
     * @return void
     */
    public function setMessage($message)
    {
        $this->setData(self::MESSAGE, $message);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getJobCode()
    {
        return $this->getData(self::JOB_CODE);
    }

    /**
     * @param $jobCode
     * @return CronHistory
     */
    public function setJobCode($jobCode)
    {
        return $this->setData(self::JOB_CODE, $jobCode);
    }

    /**
     * @return array|int|mixed|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @param $status
     * @return CronHistory
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getRequest()
    {
        return $this->getData(self::REQUEST);
    }

    /**
     * @param $request
     * @return CronHistory
     */
    public function setRequest($request)
    {
        return $this->setData(self::REQUEST, $request);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getResponse()
    {
        return $this->getData(self::RESPONSE);
    }

    /**
     * @param $response
     * @return CronHistory
     */
    public function setResponse($response)
    {
        return $this->setData(self::RESPONSE, $response);
    }

}
