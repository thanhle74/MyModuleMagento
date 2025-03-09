<?php
declare(strict_types=1);
namespace TTTech\Import\Service;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use TTTech\Import\Helper\Data as ImportHelper;
use TTTech\Import\Api\ServerUrlInterface;

class ApiService
{
    /**
     * @var Curl
     */
    protected Curl $curl;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var ImportHelper
     */
    protected ImportHelper $importHelper;

    /**
     * @param Curl $curl
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     * @param ImportHelper $importHelper
     */
    public function __construct
    (
        Curl $curl,
        SerializerInterface $serializer,
        LoggerInterface $logger,
        ImportHelper $importHelper
    ) {
        $this->curl = $curl;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->importHelper = $importHelper;
    }

    /**
     * @param string $endpoint
     * @return array
     * @throws NoSuchEntityException
     */
    public function getApiData(string $endpoint): array
    {
        $tokenData = $this->getToken();

        if (!$tokenData || !$tokenData['success']) {
            $this->logger->error("Token retrieval failed: " . ($tokenData['error'] ?? 'Unknown error'));
            return [
                'success' => false,
                'error' => 'Token retrieval failed',
                'data' => [],
                'details' => $tokenData['error'] ?? 'No token data'
            ];
        }

        $bearerToken = $tokenData['token'];
        $baseUrl = rtrim($tokenData['serverUrl'], '/') . "/api/v2";
        $apiUrl = $baseUrl . '/' . $endpoint;

        $this->curl->addHeader("Authorization", "Bearer " . $bearerToken);

        try {
            $this->curl->get($apiUrl);
            $status = $this->curl->getStatus();

            if ($status !== 200) {
                $this->logger->error("Failed to fetch data from API. Status Code: $status");
                return [
                    'success' => false,
                    'messege' => 'Failed to fetch data from API',
                    'data' => [],
                    'statusCode' => $status
                ];
            }

            $response = $this->curl->getBody();
            return [
                'success' => true,
                'messege' => 'done',
                'data' => $this->serializer->unserialize($response),
                'statusCode' => $status
            ];

        } catch (\Exception $e) {
            $this->logger->error("Error fetching data from API: " . $e->getMessage());
            return [
                'success' => false,
                'messege' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    private function getToken(): array
    {
        $params = [
            "apiKey" => $this->importHelper->getApiKey(),
            "secret" => $this->importHelper->getSecret()
        ];

        $this->curl->addHeader("Content-Type", "application/json");

        try {
            $this->curl->post(ServerUrlInterface::API_TOKEN, $this->serializer->serialize($params));
            $status = $this->curl->getStatus();

            if ($status !== 200) {
                $this->logger->error("Failed to fetch token. Status Code: $status");
                return [
                    'success' => false,
                    'error' => 'Failed to fetch token',
                    'statusCode' => $status
                ];
            }

            $responseBody = $this->curl->getBody();
            $tokenValue = $this->serializer->unserialize($responseBody);

            if (!isset($tokenValue['token']['token']['value'])) {
                $this->logger->error("Token not found in response.");
                return [
                    'success' => false,
                    'error' => 'Token not found in response'
                ];
            }

            return [
                'success' => true,
                'token' => $tokenValue['token']['token']['value'],
                'serverUrl' => $tokenValue['serverUrl'] ?? null
            ];

        } catch (\Exception $e) {
            $this->logger->error("Error fetching token: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error fetching token',
                'exceptionMessage' => $e->getMessage()
            ];
        }
    }
}
