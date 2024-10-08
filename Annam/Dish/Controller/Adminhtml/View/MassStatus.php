<?php
declare(strict_types=1);
namespace Annam\Dish\Controller\Adminhtml\View;

use Annam\Dish\Model\Dish;
use Annam\Dish\Model\ResourceModel\Dish\CollectionFactory;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Ui\Component\MassAction\Filter;
use Zend_Log_Exception;

class MassStatus extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected Filter $filter;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @var Dish
     */
    protected Dish $dishModel;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Dish $dishModel
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Dish $dishModel
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->dishModel = $dishModel;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws Zend_Log_Exception
     */
    public function execute()
    {
        $jobData = $this->collectionFactory->create();
        foreach ($jobData as $value) {
            $templateId[] = $value['id'];
        }

        $parameterData = $this->getRequest()->getParams('status');
        $selectedAppsid = $this->getRequest()->getParams('status');
        if (array_key_exists("selected", $parameterData)) {
            $selectedAppsid = $parameterData['selected'];
        }
        if (array_key_exists("excluded", $parameterData)) {
            if ($parameterData['excluded'] == 'false') {
                $selectedAppsid = $templateId;
            } else {
                $selectedAppsid = array_diff($templateId, $parameterData['excluded']);
            }
        }
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('id', ['in'=>$selectedAppsid]);
        $status = 0;
        $model=[];
        foreach ($collection as $item) {
            $this->setStatus($item->getJobId(),$this->getRequest()->getParam('status'));
            $status++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $status));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**'
     * @param $id
     * @param $Param
     * @return void
     * @throws Exception
     */
    public function setStatus($id, $Param)
    {
        $item = $this->dishModel->load($id);
        $item->setStatus($Param)->save();
        return;
    }
}
