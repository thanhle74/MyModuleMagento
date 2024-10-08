<?php
declare(strict_types=1);
namespace Annam\Import\Controller\Adminhtml\Detail;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;
use Magento\Framework\Message\ManagerInterface;
use Annam\Import\Ui\Component\HealthlabImportInterface;
use Annam\Import\Service\ImportMapping;
use Annam\Import\Service\ImportDish;
use Annam\Import\Service\ImportProduct;
use Annam\Import\Service\ImportMealPlan;
use Annam\Import\Service\Attribute\SearchByKeyword;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Annam\Import\Service\Attribute\MealPlan;
use Annam\Import\Service\Attribute\HealthlabBrand;

class Save extends Action
{
    /**
     * @var Csv
     */
    protected Csv $csv;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ImportMapping
     */
    protected ImportMapping $importMapping;

    /**
     * @var SearchByKeyword
     */
    protected SearchByKeyword $searchByKeyword;

    /**
     * @var UploaderFactory
     */
    protected UploaderFactory $uploaderFactory;

    /**
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @var MealPlan
     */
    protected MealPlan $mealPlan;

    /**
     * @var HealthlabBrand
     */
    protected HealthlabBrand $healthlabBrand;

    /**
     * @var ImportDish
     */
    protected ImportDish $importDish;

    /**
     * @var ImportProduct
     */
    protected ImportProduct $importProduct;

    /**
     * @var ImportMealPlan
     */
    protected ImportMealPlan $importMealPlan;

    /**
     * @param Context $context
     * @param Csv $csv
     * @param ManagerInterface $messageManager
     * @param ImportMapping $importMapping
     * @param SearchByKeyword $searchByKeyword
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     * @param MealPlan $mealPlan
     * @param HealthlabBrand $healthlabBrand
     * @param ImportDish $importDish
     * @param ImportProduct $importProduct
     * @param ImportMealPlan $importMealPlan
     */
    public function __construct(
        Action\Context $context,
        Csv $csv,
        ManagerInterface $messageManager,
        ImportMapping $importMapping,
        SearchByKeyword $searchByKeyword,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        MealPlan $mealPlan,
        HealthlabBrand $healthlabBrand,
        ImportDish $importDish,
        ImportProduct $importProduct,
        ImportMealPlan $importMealPlan
    ) {
        parent::__construct($context);
        $this->csv = $csv;
        $this->messageManager = $messageManager;
        $this->importMapping = $importMapping;
        $this->searchByKeyword = $searchByKeyword;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->mealPlan = $mealPlan;
        $this->healthlabBrand = $healthlabBrand;
        $this->importDish = $importDish;
        $this->importProduct = $importProduct;
        $this->importMealPlan = $importMealPlan;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        try {
            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                throw new LocalizedException(__('Invalid file upload attempt.'));
            }

            $uploader = $this->uploaderFactory->create(['fileId' => 'csv_file']);
            $uploader->setAllowedExtensions(['csv']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $result = $uploader->save($mediaDirectory->getAbsolutePath('csv'));
            if (!$result) {
                throw new LocalizedException(
                    __('File cannot be saved to the destination folder.')
                );
            };

            $filePath = $mediaDirectory->getAbsolutePath('csv') . $result['file'];
            $importData = $this->csv->getData($filePath);
            array_shift($importData);

            $handlers = [
                HealthlabImportInterface::MAPPING => $this->importMapping,
                HealthlabImportInterface::DISH => $this->importDish,
                HealthlabImportInterface::GRID_MEAL_PLAN => $this->importMealPlan,
                HealthlabImportInterface::SEARCH_BY_KEYWORD => $this->searchByKeyword,
                HealthlabImportInterface::MEAL_PLAN => $this->mealPlan,
                HealthlabImportInterface::HEALTHLAB_BRAND => $this->healthlabBrand,
                HealthlabImportInterface::PRODUCTS => $this->importProduct,
            ];

            $importSetting = $data['import_setting'];
            if (isset($handlers[$importSetting])) {
                $handlers[$importSetting]->handle($importData);
            }

            $this->messageManager->addSuccessMessage(__('CSV imported successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $this->_redirect('*/*/index');
    }
}
