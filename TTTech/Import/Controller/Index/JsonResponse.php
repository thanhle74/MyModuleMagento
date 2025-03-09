<?php
namespace TTTech\Import\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Action;

class JsonResponse extends Action
{
    protected $resultJsonFactory;

    public function __construct(Context $context, JsonFactory $resultJsonFactory)
    {
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $importProduct = $objectManager->create(\TTTech\Import\Model\ImportProduct::class);
        $importProduct->import();



//        $createCategoryService = $objectManager->create(\TTTech\Import\Service\CreateCategoryService::class);
//        echo $createCategoryService->createCategoryIfNotExists('admin1234');




        // Dữ liệu trả về JSON
        $data = [
            'success' => true,
            'message' => 'This is a JSON response.',
        ];

        return $result->setData($data);
    }
}
