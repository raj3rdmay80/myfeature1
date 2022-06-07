<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomerReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Block\Adminhtml\GroomerReview\Edit;

use Magento\Backend\Block\Widget\Context;
use Zigly\GroomerReview\Model\GroomerReviewFactory;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class StatusButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @param Context $context
     * @param GroomerReviewFactory $groomerReviewFactory
     */
    public function __construct(Context $context, GroomerReviewFactory $groomerReviewFactory)
    {
        $this->context = $context;
        $this->groomerReviewFactory = $groomerReviewFactory;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $plan = $this->groomerReviewFactory->create();
        $plan->load($this->getModelId());
        if ($this->getModelId()) {
            if ($plan->getIsActive() == '1') {
                $value = '2';
                $data = [
                    'label' => __('Not Approved'),
                    'class' => 'status',
                    'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to not approve '. $plan->getLocation() .' ?'
                    ) . '\', \'' . $this->getStatusUrl($value) . '\')',
                    'sort_order' => 20,
                ];
            } elseif ($plan->getIsActive() == '0') {
                $value = '1';
                $data = [
                    'label' => __('Approved'),
                    'class' => 'status',
                    'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to approve '. $plan->getLocation() .' ?'
                    ) . '\', \'' . $this->getStatusUrl($value) . '\')',
                    'sort_order' => 20,
                ];
            } elseif ($plan->getIsActive() == '2') {
                $value = '1';
                $data = [
                    'label' => __('Approved'),
                    'class' => 'status',
                    'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to approve '. $plan->getLocation() .' ?'
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
        return $this->getUrl('*/*/status', ['groomerreview_id' => $this->getModelId(), 'value' => $value]);
    }
}

