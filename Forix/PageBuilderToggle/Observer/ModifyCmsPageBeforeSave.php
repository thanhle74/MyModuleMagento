<?php
declare(strict_types=1);

namespace Forix\PageBuilderToggle\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class ModifyCmsPageBeforeSave implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $cmsPageData = $observer->getEvent()->getRequest()->getParams();
        $cmsPage = $observer->getEvent()->getPage();

        if ($cmsPageData['pagebuilder_content'] && (int)$cmsPageData['use_page_builder']) {
            $cmsPage->setData('content', $cmsPageData['pagebuilder_content']);
        }
    }
}
