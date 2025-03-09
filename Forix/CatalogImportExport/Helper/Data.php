<?php
declare(strict_types=1);

namespace Forix\CatalogImportExport\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\Client\Curl;

class Data extends AbstractHelper
{
    protected $scopeConfig;

    /**
     * Summary of curl
     * @var Curl
     */
    protected Curl $curl;

    /**
     * Summary of __construct
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     */
    public function __construct(
        Context $context,
        Curl $curl
    ){
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
        $this->curl = $curl;
    }

    /**
     * Summary of isYoutubeUrl
     * @param string $url
     * @return bool
     */
    public function isYoutubeUrl(string $url): bool
    {
        return str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be');
    }

    /**
     * Summary of getYoutubeId
     * @param string $url
     * @return string
     */
    public function getYoutubeId(string $url): string
    {
        preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
        return !empty($matches[1]) ? $matches[1] : '';
    }

    /**
     * Summary of getYoutubeVideoInfo
     * @param string $videoId
     */
    public function getYoutubeVideoInfo(string $videoId)
    {
        $apiKey = $this->scopeConfig->getValue('catalog/product_video/youtube_api_key');

        if (!$apiKey || !$videoId) {
            return null;
        }

        $apiUrl = "https://www.googleapis.com/youtube/v3/videos?id={$videoId}&key={$apiKey}&part=snippet";

        try {
            $this->curl->get($apiUrl);
            $response = json_decode($this->curl->getBody(), true);

            if (!empty($response['items'][0]['snippet'])) {
                return [
                    'title'       => $response['items'][0]['snippet']['title'],
                    'description' => $response['items'][0]['snippet']['description']
                ];
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}
