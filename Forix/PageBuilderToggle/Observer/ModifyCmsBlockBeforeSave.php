<?php
declare(strict_types=1);

namespace Forix\PageBuilderToggle\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Cms\Model\Block;

class ModifyCmsBlockBeforeSave implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        /** @var Block $cmsBlock */
        $cmsBlock = $observer->getEvent()->getData('object');

        if (!$cmsBlock instanceof Block) {
            return;
        }

        $pageBuilderContent = $cmsBlock->getData('pagebuilder_content');
        $usePageBuilder = (int) $cmsBlock->getData('use_page_builder');

        if (!empty($pageBuilderContent) && $usePageBuilder) {
            $cmsBlock->setData('content', $pageBuilderContent);
        }
    }
}
