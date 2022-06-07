<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Style\Color;

use Magento\Framework\Data\OptionSourceInterface;

class IconsColor implements OptionSourceInterface
{
    const DARK = 'dark';
    const LIGHT = 'light';
    const LIGHT_GREEN = 'light_green';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::DARK, 'label' => __('Dark')],
            ['value' => self::LIGHT, 'label' => __('Light')],
            ['value' => self::LIGHT_GREEN, 'label' => __('Light Green')],
        ];
    }
}
