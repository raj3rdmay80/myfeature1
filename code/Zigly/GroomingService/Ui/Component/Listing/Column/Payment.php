<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Ui\Component\Listing\Column;

class Payment implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => __('pay-now'), 'label' => __('pay-now')],
            ['value' => __('pay-none'), 'label' => __('pay-none')],
            ['value' => __('pay-later'), 'label' => __('pay-later')]
        ];
    }
}