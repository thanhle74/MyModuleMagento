<?php
declare(strict_types=1);
namespace Annam\Dish\Controller\Adminhtml\View;

use Annam\Dish\Api\DishInterface;
use Annam\Dish\Api\DishRepositoryInterface;
use Annam\Dish\Model\Dish;
use Annam\Dish\Model\ResourceModel\Dish\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\Serialize\SerializerInterface;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var Session
     */
    protected Session $adminSession;

    /**
     * @var DishRepositoryInterface
     */
    protected DishRepositoryInterface $dishRepository;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @var DishInterface
     */
    protected DishInterface $modelDish;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @param Context $context
     * @param Dish $dish
     * @param Session $adminSession
     * @param DishRepositoryInterface $dishRepository
     * @param SerializerInterface $serializer
     * @param DishInterface $modelDish
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Dish $dish,
        Session $adminSession,
        DishRepositoryInterface $dishRepository,
        SerializerInterface $serializer,
        DishInterface $modelDish,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->modelDish = $modelDish;
        $this->adminSession = $adminSession;
        $this->dishRepository = $dishRepository;
        $this->serializer = $serializer;
        $this->collectionFactory = $collectionFactory;
    }

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
                    $this->messageManager->addError(__('Dish already exists'));
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id' => $id,
                            '_current' => true
                        ]
                    );
                }

                $this->dishRepository->getById((int) $id);
            }else{
                if($collection->count())
                {
                    $this->messageManager->addError(__('Dish already exists'));
                    return $resultRedirect->setPath('*/*/add');
                }
            }

            $data['short_content'] = isset($data['short_content']) ? $this->serializer->serialize($data['short_content']) : null;
            $data['long_content'] = isset($data['long_content']) ? $this->serializer->serialize($data['long_content']) : null;
            $data['image'] = isset($data['header_logo_src']) ? $this->serializer->serialize($data['header_logo_src']) : null;
            $data['video'] = $data['video'] ?? null;
            $data['products'] = isset($data['products']) ? implode(",", $data['products'] ) : null;

            $this->modelDish->setData($data);

            try {
                $this->dishRepository->save($this->modelDish);
                $this->messageManager->addSuccess(__('The data has been saved.'));
                $this->adminSession->setFormDishData(false);
                if ($this->getRequest()->getParam('back'))
                {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    } else {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            [
                                'id' => $this->modelDish->getId(),
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

            $this->_getSession()->setFormDishData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
