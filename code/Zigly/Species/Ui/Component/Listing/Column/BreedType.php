<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Species
 */
declare(strict_types=1);

namespace Zigly\Species\Ui\Component\Listing\Column;

class BreedType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Small')],
            ['value' => 2, 'label' => __('Medium')],
            ['value' => 3, 'label' => __('Large')],
            ['value' => 4, 'label' => __('Extra Large')]
        ];
    }
}