<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Hub
 */
declare(strict_types=1);

namespace Zigly\Hub\Block\Adminhtml\Hub\Edit;

use Magento\Backend\Block\Widget\Context;
use Zigly\Hub\Model\HubFactory;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class StatusButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @param Context $context
     * @param HubFactory $hubFactory
     */
    public function __construct(Context $context, HubFactory $hubFactory)
    {
        $this->context = $context;
        $this->hubFactory = $hubFactory;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $plan = $this->hubFactory->create();
        $plan->load($this->getModelId());
        if ($this->getModelId()) {
            if ($plan->getStatus() == '1') {
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
        return $this->getUrl('*/*/status', ['hub_id' => $this->getModelId(), 'value' => $value]);
    }
}

