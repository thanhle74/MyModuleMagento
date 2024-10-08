<?php
declare(strict_types=1);
namespace Annam\Mapping\Controller\Adminhtml\Url;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Url'));
        return $resultPage;
    }
}
