<?php
declare(strict_types=1);
namespace Lkg\Base\Service;

use Lkg\Base\Api\Service\HandlerSyncInterface;
use SimpleXMLElement;

abstract class AbstractHandlerSync implements HandlerSyncInterface
{
    /**
     * @param array $params
     * @return mixed
     */
    abstract function fetchInfo(array $params): mixed;

    /**
     * @param $response
     * @return SimpleXMLElement|null
     */
    abstract function parseResponse($response): ?SimpleXMLElement;

    /**
     * @return SimpleXMLElement|null
     */
    public function processWsdlUrl(array $params): ?SimpleXMLElement
    {
        $response = $this->fetchInfo($params);

        if ($response === null) {
            return null;
        }

        return $this->parseResponse($response);
    }

    /**
     * @param array $info
     * @return array
     */
    public function getSoapOptions(array $info): array
    {
        return [
            'trace' => 1,
            'exceptions' => true,
            'login' =>  $info['login'],
            'password' => $info['password']
        ];
    }
}
