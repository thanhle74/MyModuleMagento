<?php
declare(strict_types=1);
namespace Annam\MealPlan\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Annam\HealthLab\Service\EmailSender;
use Annam\HealthLab\Helper\Data as AnnamHelper;

class SendMail extends Action
{
    /**
     * @var JsonFactory
     */
    protected JsonFactory $jsonFactory;

    /**
     * @var EmailSender
     */
    protected EmailSender $emailSender;

    /**
     * @var AnnamHelper
     */
    protected AnnamHelper $annamHelper;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param EmailSender $emailSender
     * @param AnnamHelper $annamHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        EmailSender $emailSender,
        AnnamHelper $annamHelper
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->emailSender = $emailSender;
        $this->annamHelper = $annamHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        try {
            $postData = $this->getRequest()->getPostValue();

            $this->emailSender->sendEmail(
                $this->annamHelper->emailTemplate(),
                [
                    'name' => $this->annamHelper->getNameCustomerSupport(),
                    'email' => $this->annamHelper->getEmailCustomerSupport(),
                ],
                [
                    'name' => $this->annamHelper->emailNameMealPlan(),
                    'email' => $this->annamHelper->emailAddressMealPlan(),
                ],
                [
                    'name' => $postData['name'],
                    'email' => $postData['email'],
                    'content' => $postData['content'],
                ]
            );

            $result->setData(['success' => true, 'message' => 'Email sent successfully.']);
        } catch (\Exception $e) {
            $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }

        return $result;
    }
}
