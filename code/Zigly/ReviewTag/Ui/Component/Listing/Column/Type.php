<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Ui\Component\Listing\Column;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    //Here you can __construct Model

    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Groomer')],
            ['value' => 2, 'label' => __('Vet Consultant')],
            ['value' => 3, 'label' => __('Product')]
        ];
    }
}