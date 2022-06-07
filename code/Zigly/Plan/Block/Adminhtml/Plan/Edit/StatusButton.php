<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Plan
 */
declare(strict_types=1);

namespace Zigly\Plan\Block\Adminhtml\Plan\Edit;

use Magento\Backend\Block\Widget\Context;
use Zigly\Plan\Model\PlanFactory;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class StatusButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @param Context $context
     * @param PlanFactory $PlanFactory
     */
    public function __construct(Context $context, PlanFactory $planFactory)
    {
        $this->context = $context;
        $this->planFactory = $planFactory;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $plan = $this->planFactory->create();
        $plan->load($this->getModelId());
        if ($this->getModelId()) {
            if ($plan->getStatus() == '1') {
                $value = '0';
                $data = [
                    'label' => __('Disable'),
                    'class' => 'status',
                    'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to disable '. $plan->getPlanName() .' ?'
                    ) . '\', \'' . $this->getStatusUrl($value) . '\')',
                    'sort_order' => 20,
                ];
            } else {
                $value = '1';
                $data = [
                    'label' => __('Enable'),
                    'class' => 'status',
                    'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to enable '. $plan->getPlanName() .' ?'
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
        return $this->getUrl('*/*/status', ['plan_id' => $this->getModelId(), 'value' => $value]);
    }
}

