<?php
declare(strict_types=1);
namespace Annam\HealthLab\Service;

use Dompdf\Dompdf;
use Exception;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Exception\FileSystemException;

class PdfService extends AbstractHelper
{
    protected DirectoryList $directoryList;
    protected File $file;

    public function __construct(Context $context, DirectoryList $directoryList, File $file)
    {
        parent::__construct($context);
        $this->directoryList = $directoryList;
        $this->file = $file;
    }

    /**
     * @throws Exception
     */
    public function createPdfFromHtml($htmlContent): string
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        try {
            $varDir = $this->directoryList->getPath(DirectoryList::VAR_DIR);
        } catch (FileSystemException $e) {
            throw new Exception('Cannot access the var directory: ' . $e->getMessage());
        }
        $pdfDir = $varDir . '/pdf';
        if (!$this->file->fileExists($pdfDir)) {
            $this->file->mkdir($pdfDir, 0755);
        }

        $pdfFilePath = $pdfDir . '/file.pdf';

        file_put_contents($pdfFilePath, $dompdf->output());

        return $pdfFilePath;
    }
}
