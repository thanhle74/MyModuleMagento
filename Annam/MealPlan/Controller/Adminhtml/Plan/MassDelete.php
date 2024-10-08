<?php
declare(strict_types=1);
namespace Annam\MealPlan\Controller\Adminhtml\Plan;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Annam\MealPlan\Model\ResourceModel\Meal\CollectionFactory;
use Annam\MealPlan\Api\MealRepositoryInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @var MealRepositoryInterface
     */
    protected MealRepositoryInterface $mealRepository;

    /**
     * @var Filter
     */
    protected Filter $filter;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param MealRepositoryInterface $mealRepository
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        MealRepositoryInterface  $mealRepository,
        Filter $filter
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->mealRepository = $mealRepository;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $delete = 0;
        foreach ($collection as $model) {
            $this->mealRepository->deleteById((int)$model->getId());
            $delete++;
        }

        $this->messageManager->addSuccess(__('A total of %1 Records have been deleted.', $delete));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
