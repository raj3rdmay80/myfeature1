<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Plan
 */
declare(strict_types=1);

namespace Zigly\Plan\Ui\Component\Listing\Column;

class PlanType implements \Magento\Framework\Option\ArrayInterface
{
    //Here you can __construct Model

    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('At Home')],
            ['value' => 2, 'label' => __('At Experience Center')],
            ['value' => 3, 'label' => __('Both')]
        ];
    }
}