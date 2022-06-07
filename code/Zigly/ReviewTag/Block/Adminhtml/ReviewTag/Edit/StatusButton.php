<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Block\Adminhtml\ReviewTag\Edit;

use Magento\Backend\Block\Widget\Context;
use Zigly\ReviewTag\Model\ReviewTagFactory;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class StatusButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @param Context $context
     * @param ReviewTagFactory $ReviewTagFactory
     */
    public function __construct(Context $context, ReviewTagFactory $reviewTagFactory)
    {
        $this->context = $context;
        $this->reviewTagFactory = $reviewTagFactory;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $plan = $this->reviewTagFactory->create();
        $plan->load($this->getModelId());
        if ($this->getModelId()) {
            if ($plan->getIsActive() == '1') {
                $value = '0';
                $data = [
                    'label' => __('Disable'),
                    'class' => 'status',
                    'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to disable '. $plan->getLocation() .' ?'
                    ) . '\', \'' . $this->getStatusUrl($value) . '\')',
                    'sort_order' => 20,
                ];
            } else {
                $value = '1';
                $data = [
                    'label' => __('Enable'),
                    'class' => 'status',
                    'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to enable '. $plan->getLocation() .' ?'
                    ) . '\', \'' . $this->getStatusUrl($value) . '\')',
                    'sort_order' => 20,
                ];
            }
        }
        return $data;
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    public function getStatusUrl($value)
    {
        return $this->getUrl('*/*/status', ['reviewtag_id' => $this->getModelId(), 'value' => $value]);
    }
}

