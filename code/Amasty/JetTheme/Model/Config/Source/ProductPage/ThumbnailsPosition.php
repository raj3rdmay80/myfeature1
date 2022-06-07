<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Config\Source\ProductPage;

use Magento\Framework\Data\OptionSourceInterface;

class ThumbnailsPosition implements OptionSourceInterface
{
    const BOTTOM = 'bottom';
    const LEFT = 'left';
    const RIGHT = 'right';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::BOTTOM, 'label' => __('Bottom')],
            ['value' => self::LEFT, 'label' => __('Left')],
            ['value' => self::RIGHT, 'label' => __('Right')],
        ];
    }
}
