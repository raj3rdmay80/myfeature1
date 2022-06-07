<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Type
 */
class Type implements ArrayInterface
{
    const CUSTOM = 0;
    const MAGENTO = 1;
    const ANOTHER_MODULES = 2;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::CUSTOM,
                'label' => __('Custom')
            ],
            [
                'value' => self::MAGENTO,
                'label' => __('Default')
            ],
            [
                'value' => self::ANOTHER_MODULES,
                'label' => __('3rd-party')
            ]
        ];
    }
}
