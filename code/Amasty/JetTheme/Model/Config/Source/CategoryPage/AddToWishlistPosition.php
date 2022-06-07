<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Config\Source\CategoryPage;

use Magento\Framework\Data\OptionSourceInterface;

class AddToWishlistPosition implements OptionSourceInterface
{
    const NO = 0;
    const TOP_LEFT = 'top-left';
    const TOP_LEFT_HOVER = 'top-left-hover';
    const TOP_RIGHT = 'top-right';
    const TOP_RIGHT_HOVER = 'top-right-hover';
    const BOTTOM_RIGHT = 'bottom-right';
    const BOTTOM_RIGHT_HOVER = 'bottom-right-hover';
    const BOTTOM_LEFT = 'bottom-left';
    const BOTTOM_LEFT_HOVER = 'bottom-left-hover';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::NO, 'label' => __('No')],
            ['value' => self::TOP_LEFT, 'label' => __('Top Left')],
            ['value' => self::TOP_LEFT_HOVER, 'label' => __('Top Left on Hover')],
            ['value' => self::TOP_RIGHT, 'label' => __('Top Right')],
            ['value' => self::TOP_RIGHT_HOVER, 'label' => __('Top Right on Hover')],
            ['value' => self::BOTTOM_RIGHT, 'label' => __('Bottom Right')],
            ['value' => self::BOTTOM_RIGHT_HOVER, 'label' => __('Bottom Right on Hover')],
            ['value' => self::BOTTOM_LEFT, 'label' => __('Bottom Left')],
            ['value' => self::BOTTOM_LEFT_HOVER, 'label' => __('Bottom Left on Hover')],
        ];
    }
}
