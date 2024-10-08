<?php
declare(strict_types=1);
namespace Lkg\Base\Api\Data;

use DateTime;

interface CronHistoryInterface
{
    public const JOB_CODE = 'job_code';
    public const STATUS = 'status';
    public const REQUEST = 'request';
    public const RESPONSE = 'response';
    public const MESSAGE = 'message';
    public const EXECUTED_AT = 'executed_at';
    public const PUBLISHER_ID = 'publisher_id';
    /**#@-*/

    /**
     * @return mixed
     */
    public function getPublisherId();

    /**
     * @param $publisher_id
     * @return mixed
     */
    public function setPublisherId($publisher_id);

    /**
     * @return DateTime|null
     */
    public function getExecutedAt(): ?DateTime;

    /**
     * @param DateTime $dateTime
     * @return mixed
     */
    public function setExecutedAt(DateTime $dateTime);

    /**
     * @return mixed
     */
    public function getMessage();

    /**
     * @param $message
     * @return mixed
     */
    public function setMessage($message);

    /**
     * Get Job Code
     *
     * @return string|null
     */
    public function getJobCode();

    /**
     * Set Job Code
     *
     * @param string $jobCode
     * @return $this
     */
    public function setJobCode($jobCode);

    /**
     * Get Status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set Status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get Request
     *
     * @return string|null
     */
    public function getRequest();

    /**
     * Set Request
     *
     * @param string $request
     * @return $this
     */
    public function setRequest($request);

    /**
     * Get Response
     *
     * @return string|null
     */
    public function getResponse();

    /**
     * Set Response
     *
     * @param string $response
     * @return $this
     */
    public function setResponse($response);
}
