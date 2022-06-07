<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Config\Source\Newsletter;

use Magento\Framework\Data\OptionSourceInterface;

class FormPosition implements OptionSourceInterface
{
    const LEFT = 'left';
    const RIGHT = 'right';
    const CENTER = 'center';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::LEFT, 'label' => __('Left')],
            ['value' => self::RIGHT, 'label' => __('Right')],
            ['value' => self::CENTER, 'label' => __('Center')],
        ];
    }
}
