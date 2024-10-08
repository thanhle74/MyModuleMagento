<?php
declare(strict_types=1);
namespace Annam\Ingredients\Controller\Adminhtml\View;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Annam\Ingredients\Model\Ingredients;
use Annam\Ingredients\Model\ResourceModel\Ingredients\CollectionFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var Ingredients
     */
    protected Ingredients $ingredients;

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

    /**
     * @param Context $context
     * @param Ingredients $ingredients
     * @param SerializerInterface $serializer
     * @param Session $adminSession
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Action\Context $context,
        Ingredients $ingredients,
        SerializerInterface $serializer,
        Session $adminSession,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->ingredients = $ingredients;
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
                    $this->messageManager->addError(__('Ingredients already exists'));
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id' => $id,
                            '_current' => true
                        ]
                    );
                }

                $this->ingredients->load($id);
            }else{
                if($collection->count())
                {
                    $this->messageManager->addError(__('Ingredients already exists'));
                    return $resultRedirect->setPath('*/*/add');
                }
            }

            $data['store'] = isset($data['store']) ? $this->serializer->serialize($data['store']) : null;
            $data['short_content'] = isset($data['short_content']) ? $this->serializer->serialize($data['short_content']) : null;
            $data['ingredients'] = isset($data['ingredients']) ? $this->serializer->serialize($data['ingredients']) : null;
            $data['image'] = isset($data['header_logo_src']) ? $this->serializer->serialize($data['header_logo_src']) : null;
            $data['banner'] = isset($data['head_shortcut_icon']) ? $this->serializer->serialize($data['head_shortcut_icon']) : null;

            $this->ingredients->setData($data);

            try {
                $this->ingredients->save();
                $this->messageManager->addSuccess(__('The data has been saved.'));
                $this->adminSession->setExploreFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    } else {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            [
                                'id' => $this->ingredients->getId(),
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
