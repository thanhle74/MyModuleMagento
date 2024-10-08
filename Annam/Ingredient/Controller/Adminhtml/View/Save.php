<?php
declare(strict_types=1);
namespace Annam\Ingredient\Controller\Adminhtml\View;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Annam\Ingredient\Model\Ingredient;
use Annam\Ingredient\Model\ResourceModel\Ingredient\CollectionFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var Ingredient
     */
    protected Ingredient $ingredient;

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
     * @param Ingredient $ingredient
     * @param SerializerInterface $serializer
     * @param Session $adminSession
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Action\Context $context,
        Ingredient $ingredient,
        SerializerInterface $serializer,
        Session $adminSession,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->ingredient = $ingredient;
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
                    $this->messageManager->addError(__('Ingredient already exists'));
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id' => $id,
                            '_current' => true
                        ]
                    );
                }

                $this->ingredient->load($id);
            }else{
                if($collection->count())
                {
                    $this->messageManager->addError(__('Ingredient already exists'));
                    return $resultRedirect->setPath('*/*/add');
                }
            }

            $data['store'] = isset($data['store_id']) ? $this->serializer->serialize($data['store_id']) : null;
            $data['short_content'] = isset($data['short_content']) ? $this->serializer->serialize($data['short_content']) : null;
            $data['image'] = isset($data['header_logo_src']) ? $this->serializer->serialize($data['header_logo_src']) : null;
            $data['nutritional_ingredients'] = isset($data['nutritional_ingredients']) ? $this->serializer->serialize($data['nutritional_ingredients']) : null;
            $data['skus'] = isset($data['products']) ? $this->serializer->serialize($data['products']) : null;

            $this->ingredient->setData($data);

            try {
                $this->ingredient->save();
                $this->messageManager->addSuccess(__('The data has been saved.'));
                $this->adminSession->setExploreFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    } else {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            [
                                'id' => $this->ingredient->getId(),
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
