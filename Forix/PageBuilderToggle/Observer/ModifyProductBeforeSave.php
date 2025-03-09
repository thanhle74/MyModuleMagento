<?php
declare(strict_types=1);

namespace Forix\PageBuilderToggle\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ModifyProductBeforeSave implements ObserverInterface
{
    /**
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $product = $observer->getEvent()->getProduct();

        if (!$product) {
            return;
        }

        if ((int) $product->getData('use_page_builder') === 1) {
            $product->setData('description', $product->getData('pagebuilder_content'));
        }
    }
}
