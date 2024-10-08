<?php
declare(strict_types=1);
namespace Annam\HealthLab\Service;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem\Io\File;
use Annam\HealthLab\Helper\Data as AnnamHelper;

class EmailSenderPdf extends AbstractHelper
{
    protected TransportBuilder $transportBuilder;
    protected StateInterface $inlineTranslation;
    protected StoreManagerInterface $storeManager;
    protected File $file;
    protected AnnamHelper $annamHelper;

    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        StoreManagerInterface $storeManager,
        File $file,
        AnnamHelper $annamHelper
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
        $this->file = $file;
        $this->annamHelper = $annamHelper;
        parent::__construct($context);
    }

    /**
     * @param $recipientEmail
     * @param $pdfFilePath
     * @return void
     * @throws NoSuchEntityException
     */
    public function sendEmailWithAttachment($recipientEmail, $pdfFilePath)
    {
        $this->inlineTranslation->suspend();

        $sender = [
            'name' => $this->annamHelper->getNameCustomerSupport(),
            'email' => $this->annamHelper->getEmailCustomerSupport(),
        ];

        $transport = $this->transportBuilder
            ->setTemplateIdentifier($this->annamHelper->emailTemplate())
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId(),
            ])
            ->setTemplateVars([])
            ->setFrom($sender)
            ->addTo($recipientEmail)
            ->addAttachment(
                $this->file->read($pdfFilePath),
                'application/pdf',
                'attachment.pdf'
            )
            ->getTransport();

        $transport->sendMessage();

        $this->inlineTranslation->resume();
    }
}
