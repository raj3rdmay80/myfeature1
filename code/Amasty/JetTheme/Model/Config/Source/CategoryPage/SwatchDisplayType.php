<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Config\Source\CategoryPage;

use Magento\Framework\Data\OptionSourceInterface;

class SwatchDisplayType implements OptionSourceInterface
{
    const HOVER = 'hover';
    const ALWAYS = 'always';
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::HOVER, 'label' => __('Hover')],
            ['value' => self::ALWAYS, 'label' => __('Always')],
        ];
    }
}
