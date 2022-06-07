<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Groomer
 */
declare(strict_types=1);

namespace Zigly\Groomer\Ui\Component\Listing\Column;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    //Here you can __construct Model

    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Under Review')],
            ['value' => 1, 'label' => __('Approved')],
            ['value' => 2, 'label' => __('On-Hold')],
            ['value' => 3, 'label' => __('Rejected')]
        ];
    }
}