<?php
declare(strict_types=1);
namespace Annam\HealthLab\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Area;
use Annam\HealthLab\Api\EmailSenderInterface;

class EmailSender implements EmailSenderInterface
{
    /**
     * @var TransportBuilder
     */
    protected TransportBuilder $transportBuilder;

    /**
     * @var StateInterface
     */
    protected StateInterface $inlineTranslation;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $templateId
     * @param $sender
     * @param $receiver
     * @param array $templateVars
     * @return void
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function sendEmail($templateId, $sender, $receiver, array $templateVars = []): void
    {
        $storeId = $this->storeManager->getStore()->getId();
        $templateOptions = [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];

        $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($sender)
            ->addTo($receiver['email'], $receiver['name'])
            ->getTransport();

        $this->inlineTranslation->suspend();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
}
