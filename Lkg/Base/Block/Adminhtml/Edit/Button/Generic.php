<?php
declare(strict_types=1);
namespace Lkg\Base\Block\Adminhtml\Edit\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Cms\Api\PageRepositoryInterface;

class Generic
{
    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @var PageRepositoryInterface
     */
    protected PageRepositoryInterface $pageRepository;

    /**
     * @param Context $context
     * @param PageRepositoryInterface $pageRepository
     */
    public function __construct(
        Context $context,
        PageRepositoryInterface $pageRepository
    ) {
        $this->context = $context;
        $this->pageRepository = $pageRepository;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
