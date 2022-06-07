<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Activities
 */
declare(strict_types=1);

namespace Zigly\Activities\Block\Adminhtml\Activities\Edit;

use Magento\Backend\Block\Widget\Context;
use Zigly\Activities\Model\ActivitiesFactory;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class StatusButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @param Context $context
     * @param ActivitiesFactory $activitiesFactory
     */
    public function __construct(Context $context, ActivitiesFactory $activitiesFactory)
    {
        $this->context = $context;
        $this->activitiesFactory = $activitiesFactory;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $activity = $this->activitiesFactory->create();
        $activity->load($this->getModelId());
        if ($this->getModelId()) {
            if ($activity->getIsActive() == '1') {
                $value = '0';
                $data = [
                    'label' => __('Disable'),
                    'class' => 'status',
                    'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to disable '. $activity->getActivityName() .' ?'
                    ) . '\', \'' . $this->getStatusUrl($value) . '\')',
                    'sort_order' => 20,
                ];
            } else {
                $value = '1';
                $data = [
                    'label' => __('Enable'),
                    'class' => 'status',
                    'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to enable '. $activity->getActivityName() .' ?'
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
        return $this->getUrl('*/*/status', ['activities_id' => $this->getModelId(), 'value' => $value]);
    }
}

