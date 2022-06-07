<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Block\Adminhtml\Grooming\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class EditButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Edit'),
            'class' => 'edit',
            'on_click' => sprintf("location.href = '%s';", $this->getEditUrl()),
            'sort_order' => 90,
        ];
    }

    /**
     * Get URL for edit
     *
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl('zigly_groomingservice/grooming/edit', ['entity_id' => $this->getModelId()]);
    }
}

