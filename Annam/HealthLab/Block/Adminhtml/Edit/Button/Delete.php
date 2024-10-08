<?php
declare(strict_types=1);
namespace Annam\HealthLab\Block\Adminhtml\Edit\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete extends Generic implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $data = [];
        $id = $this->context->getRequest()->getParam('id');
        if ($id) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl(): string
    {
        $id = $this->context->getRequest()->getParam('id');
        return $this->getUrl('*/*/delete', ['id' => $id]);
    }
}
