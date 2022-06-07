<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_CouponService
 */
declare(strict_types=1);

namespace Zigly\CouponService\Block\Adminhtml\CouponService\Edit;

use Magento\Backend\Block\Widget\Context;
use Zigly\CouponService\Model\CouponServiceFactory;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class StatusButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @param Context $context
     * @param CouponServiceFactory $couponServiceFactory
     */
    public function __construct(Context $context, CouponServiceFactory $couponServiceFactory)
    {
        $this->context = $context;
        $this->couponServiceFactory = $couponServiceFactory;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $coupon = $this->couponServiceFactory->create();
        $coupon->load($this->getModelId());
        if ($this->getModelId()) {
            if ($coupon->getStatus() == '1') {
                $value = '0';
                $data = [
                    'label' => __('Disable'),
                    'class' => 'status',
                    'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to disable '. $coupon->getName() .' ?'
                    ) . '\', \'' . $this->getStatusUrl($value) . '\')',
                    'sort_order' => 20,
                ];
            } else {
                $value = '1';
                $data = [
                    'label' => __('Enable'),
                    'class' => 'status',
                    'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to enable '. $coupon->getName() .' ?'
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
        return $this->getUrl('*/*/status', ['couponservice_id' => $this->getModelId(), 'value' => $value]);
    }
}

