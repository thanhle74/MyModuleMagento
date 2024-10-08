<?php
declare(strict_types=1);
namespace Annam\HealthLab\Block;

use Magento\Framework\View\Element\Template;

class CreateAccountForm extends Template
{
    /**
     * @return string
     */
    public function getFormAction(): string
    {
        return $this->getUrl('healthlab/popup/create');
    }
}
