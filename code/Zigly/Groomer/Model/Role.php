<?php
/**
* Copyright (C) 2021  Zigly
* @package   Zigly_Groomer
*/
namespace Zigly\Groomer\Model;

use Magento\Framework\Data\OptionSourceInterface;

class Role implements OptionSourceInterface
{

    /**
     * Get state type labels array.
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            0 => [
                'label' => 'Trainer',
                'value' => '1'
            ],
            1 => [
                'label' => 'Groomer',
                'value' => '2'
            ],
            2 => [
                'label' => 'Vet',
                'value' => '3'
            ],
            3 => [
                'label' => 'Behaviorist',
                'value' => '4'
            ],
        ];

        return $options;
     }
}
