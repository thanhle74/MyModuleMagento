<?php
declare(strict_types=1);
namespace Annam\MealPlan\Controller\Adminhtml\Plan;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\Serialize\SerializerInterface;
use Annam\MealPlan\Api\MealRepositoryInterface;
use Annam\MealPlan\Api\MealInterface;
use Annam\MealPlan\Model\ResourceModel\Meal\CollectionFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var MealInterface
     */
    protected MealInterface $meal;

    /**
     * @var Session
     */
    protected Session $adminSession;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @var MealRepositoryInterface
     */
    protected MealRepositoryInterface $mealRepository;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @param Context $context
     * @param MealInterface $meal
     * @param SerializerInterface $serializer
     * @param Session $adminSession
     * @param MealRepositoryInterface $mealRepository
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Action\Context $context,
        MealInterface $meal,
        SerializerInterface $serializer,
        Session $adminSession,
        MealRepositoryInterface $mealRepository,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->meal = $meal;
        $this->adminSession = $adminSession;
        $this->serializer = $serializer;
        $this->mealRepository = $mealRepository;
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
                    $this->messageManager->addError(__('Meal Plan already exists'));
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id' => $id,
                            '_current' => true
                        ]
                    );
                }

                $this->mealRepository->getById((int)$id);
            }else{
                if($collection->count())
                {
                    $this->messageManager->addError(__('Meal Plan already exists'));
                    return $resultRedirect->setPath('*/*/add');
                }
            }

            for ($i = 1; $i <= 7; $i++) {
                $dayKey = 'day_' . $i;
                $fieldsetKey = 'fieldset_day_' . $i;
                $data[$dayKey] = isset($data[$fieldsetKey]['list_dish']) ? $this->serializer->serialize($data[$fieldsetKey]['list_dish']) : null;
            }
            $data['content'] = isset($data['content']) ? $this->serializer->serialize($data['content']) : null;
            $data['store'] = isset($data['store_id']) ? $this->serializer->serialize($data['store_id']) : null;
            $data['image'] = isset($data['header_logo_src']) ? $this->serializer->serialize($data['header_logo_src']) : null;

            $this->meal->setData($data);
            try {
                $this->mealRepository->save($this->meal);
                $this->messageManager->addSuccess(__('The data has been saved.'));
                $this->adminSession->setMealFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    } else {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            [
                                'id' => $this->meal->getId(),
                                '_current' => true
                            ]
                        );
                    }
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setMealFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
