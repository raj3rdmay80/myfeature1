<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */

namespace Zigly\GroomingService\Model\Config\Source\Timeslot;

class Minutes implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '00', 'label' => __('00')],
            ['value' => '30', 'label' => __('30')]
        ];
    }
}