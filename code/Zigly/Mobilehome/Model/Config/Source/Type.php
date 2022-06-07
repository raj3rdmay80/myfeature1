<?php
/**
 
 */

namespace Zigly\Mobilehome\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Type
 * @package Zigly\MobileHome\Model\Config\Source
 */
class Type implements ArrayInterface
{
    const BANNER = '0';
    const CATEGROY = '1';
    const BRAND = '2';
    const OFFER = '3';

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::BANNER,
                'label' => __('Banner')
            ],
            [
                'value' => self::CATEGROY,
                'label' => __('Category')
            ],
            [
                'value' => self::BRAND,
                'label' => __('Brand')
            ],
            [
                'value' => self::OFFER,
                'label' => __('Offer')
            ]
        ];

        return $options;
    }
}
