<?php
declare(strict_types=1);
namespace Annam\Insights\Controller\Adminhtml\View;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Annam\Insights\Model\Insights;
use Annam\Insights\Model\ResourceModel\Insights\CollectionFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var Insights
     */
    protected Insights $insights;

    /**
     * @var Session
     */
    protected Session $adminSession;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    public function __construct(
        Action\Context $context,
        Insights $insights,
        SerializerInterface $serializer,
        Session $adminSession,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->insights = $insights;
        $this->adminSession = $adminSession;
        $this->serializer = $serializer;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $id = $this->getRequest()->getParam('id');

            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('name' , ['eq' => trim(strtolower($data['name']))]);

            if ($id) {
                $collection->addFieldToFilter('id', ['neq' => $id]);
                if($collection->count())
                {
                    $this->messageManager->addError(__('Infographic already exists'));
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id' => $id,
                            '_current' => true
                        ]
                    );
                }

                $this->insights->load($id);
            }else{
                if($collection->count())
                {
                    $this->messageManager->addError(__('Infographic already exists'));
                    return $resultRedirect->setPath('*/*/add');
                }
            }

            $data['store'] = isset($data['store']) ? $this->serializer->serialize($data['store']) : null;
            $data['infographic_ingredients'] = isset($data['infographic_ingredients']) ? $this->serializer->serialize($data['infographic_ingredients']) : null;
            $data['definition'] = isset($data['definition']) ? $this->serializer->serialize($data['definition']) : null;
            $data['benefits'] = isset($data['benefits']) ? $this->serializer->serialize($data['benefits']) : null;
            $data['image'] = isset($data['header_logo_src']) ? $this->serializer->serialize($data['header_logo_src']) : null;

            $this->insights->setData($data);

            try {
                $this->insights->save();
                $this->messageManager->addSuccess(__('The data has been saved.'));
                $this->adminSession->setExploreFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    } else {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            [
                                'id' => $this->insights->getId(),
                                '_current' => true
                            ]
                        );
                    }
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException|\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setExploreFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
