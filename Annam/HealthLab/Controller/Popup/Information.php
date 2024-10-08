<?php
declare(strict_types=1);
namespace Annam\HealthLab\Controller\Popup;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Annam\HealthLab\Service\EmailSenderPdf;
use Annam\HealthLab\Service\PdfService;

class Information extends Action
{
    protected EmailSenderPdf $emailSenderPdf;
    protected PdfService $pdfService;

    public function __construct(
        Context $context,
        EmailSenderPdf $emailSenderPdf,
        PdfService $pdfService
    ) {
        parent::__construct($context);
        $this->emailSenderPdf = $emailSenderPdf;
        $this->pdfService = $pdfService;
    }

    public function execute()
    {
        try {
            $htmlContent = '<html><body><h1>Hello, World!</h1></body></html>';
            $pdfFilePath = $this->pdfService->createPdfFromHtml($htmlContent);
            $recipientEmail = 'ldthanhqt@gmail.com';

            $this->emailSenderPdf->sendEmailWithAttachment($recipientEmail, $pdfFilePath);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error: ') . $e->getMessage());
        }
    }
}
