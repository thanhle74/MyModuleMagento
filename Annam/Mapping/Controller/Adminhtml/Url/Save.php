<?php
declare(strict_types=1);
namespace Annam\Mapping\Controller\Adminhtml\Url;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Annam\Mapping\Model\Mapping;
use Annam\Mapping\Model\ResourceModel\Mapping\CollectionFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var Mapping
     */
    protected Mapping $mapping;

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
        Mapping $mapping,
        SerializerInterface $serializer,
        Session $adminSession,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->mapping = $mapping;
        $this->adminSession = $adminSession;
        $this->serializer = $serializer;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);

        $data = $this->getRequest()->getPostValue();// get from post request;

        $resultRedirect = $this->resultRedirectFactory->create();
        // $logger->info(print_r($data,true));
        if ($data) {
            $data['url_vn'] = trim($data['url_vn'], '/');
            $data['url_en'] = trim($data['url_en'], '/');
            // $logger->info(print_r($data,true));
            $id = $this->getRequest()->getParam('id');

            $collection = $this->collectionFactory->create();

            if ($id) { // Edit action check

                $collection->addFieldToFilter('id', ['neq' => $id]);

                $collection->addFieldToFilter( // find all record have same url
                    ['url_vn', 'url_en'],
                    [
                        ['like' => '%' . $data['url_vn'] . '%'],
                        ['like' => '%' . $data['url_en'] . '%']
                    ]
                );

                // $logger->info($collection->getSelect()->__toString());

                if($collection->count()){
                    $this->messageManager->addError(__('These urls VN or EN already exists!'));
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id' => $id,
                            '_current' => true
                        ]
                    );
                }
                $this->mapping->load($id);
            }else{// Add action check

                $collection->addFieldToFilter( // find all record have same url
                    ['url_vn', 'url_en'],
                    [
                        ['like' => '%' . $data['url_vn'] . '%'],
                        ['like' => '%' . $data['url_en'] . '%']
                    ]
                );

                if($collection->count())
                {
                    $this->messageManager->addError(__('These urls VN or EN already exists!'));
                    return $resultRedirect->setPath('*/*/add');
                }
            }

            $this->mapping->setData($data);

            try {
                $this->mapping->save();
                $this->messageManager->addSuccess(__('The data has been saved.'));
                $this->adminSession->setExploreFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    } else {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            [
                                'id' => $this->mapping->getId(),
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
