<?php
declare(strict_types=1);

namespace Forix\PageBuilderToggle\Plugin;

use Magento\Cms\Model\Block\DataProvider;
use Magento\Framework\App\RequestInterface;

class CmsBlockDataProviderPlugin
{
    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ){
        $this->request = $request;
    }

    /**
     * @param DataProvider $subject
     * @param $result
     */
    public function afterGetData(DataProvider $subject, $result)
    {
        $pageId = (int) $this->request->getParam('block_id');
        if ($pageId > 0 && isset($result[$pageId]) && !empty($result[$pageId]['content'])) {
            $result[$pageId]['pagebuilder_content'] = $result[$pageId]['content'];
        }

        return $result;
    }
}
