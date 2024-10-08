<?php
declare(strict_types=1);
namespace Annam\HealthLab\Api;

interface EmailSenderInterface
{
    /**
     * @param $templateId
     * @param $sender
     * @param $receiver
     * @param array $templateVars
     * @return void
     */
    public function sendEmail($templateId, $sender, $receiver, array $templateVars = []): void;
}
