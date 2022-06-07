<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */

namespace Zigly\VetConsulting\Model\Config\Source\Timeslot;

class Hours implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('1')],
            ['value' => '2', 'label' => __('2')],
            ['value' => '3', 'label' => __('3')],
            ['value' => '4', 'label' => __('4')],
            ['value' => '5', 'label' => __('5')],
            ['value' => '6', 'label' => __('6')],
            ['value' => '7', 'label' => __('7')],
            ['value' => '8', 'label' => __('8')],
            ['value' => '9', 'label' => __('9')],
            ['value' => '10', 'label' => __('10')],
            ['value' => '11', 'label' => __('11')],
            ['value' => '12', 'label' => __('12')],
            ['value' => '13', 'label' => __('13')],
            ['value' => '14', 'label' => __('14')],
            ['value' => '15', 'label' => __('15')],
            ['value' => '16', 'label' => __('16')],
            ['value' => '17', 'label' => __('17')],
            ['value' => '18', 'label' => __('18')],
            ['value' => '19', 'label' => __('19')],
            ['value' => '20', 'label' => __('20')],
            ['value' => '21', 'label' => __('21')],
            ['value' => '22', 'label' => __('22')],
            ['value' => '23', 'label' => __('23')]
        ];
    }
}