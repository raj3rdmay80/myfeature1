<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Groomer
 */
declare(strict_types=1);

namespace Zigly\Groomer\Ui\Component\Listing\Column;

class Role implements \Magento\Framework\Option\ArrayInterface
{
    //Here you can __construct Model

    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Trainer')],
            ['value' => 2, 'label' => __('Groomer')],
            ['value' => 3, 'label' => __('Vet')],
            ['value' => 4, 'label' => __('Behaviorist')]
        ];
    }
}