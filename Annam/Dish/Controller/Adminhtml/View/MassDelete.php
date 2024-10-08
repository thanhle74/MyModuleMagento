<?php
declare(strict_types=1);
namespace Annam\Dish\Controller\Adminhtml\View;

use Annam\Dish\Api\DishRepositoryInterface;
use Annam\Dish\Model\ResourceModel\Dish\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
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
     * @var DishRepositoryInterface
     */
    protected DishRepositoryInterface $dishRepository;

    /**
     * @var Filter
     */
    protected Filter $filter;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param DishRepositoryInterface $dishRepository
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        DishRepositoryInterface $dishRepository,
        Filter $filter
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->dishRepository = $dishRepository;
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
            $this->dishRepository->deleteById((int)$model->getId());
            $delete++;
        }

        $this->messageManager->addSuccess(__('A total of %1 Records have been deleted.', $delete));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
