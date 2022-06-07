<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Cityscreen
 */
declare(strict_types=1);

namespace Zigly\Cityscreen\Ui\Component\Listing\Column;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    //Here you can __construct Model

    public function toOptionArray()
    {
        return [
            ['value' => 2, 'label' => __('Others')],
            ['value' => 1, 'label' => __('Delhi')]
        ];
    }
}