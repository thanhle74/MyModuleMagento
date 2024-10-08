<?php
declare(strict_types=1);
namespace Lkg\Base\Api\Service;

use SimpleXMLElement;

interface HandlerSyncInterface
{
    /**
     * @return SimpleXMLElement|null
     */
    public function processWsdlUrl(array $params): ?SimpleXMLElement;

    /**
     * @param array $params
     * @return mixed
     */
    public function fetchInfo(array $params): mixed;

    /**
     * @param array $info
     * @return array
     */
    public function getSoapOptions(array $info): array;

    /**
     * @param $response
     * @return SimpleXMLElement|null
     */
    public function parseResponse($response): ?SimpleXMLElement;
}
