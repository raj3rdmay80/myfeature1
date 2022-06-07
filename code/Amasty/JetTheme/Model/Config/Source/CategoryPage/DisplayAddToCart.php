<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Config\Source\CategoryPage;

use Magento\Framework\Data\OptionSourceInterface;

class DisplayAddToCart implements OptionSourceInterface
{
    const NO = 'none';
    const ALWAYS = 'always';
    const HOVER = 'hover';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::NO, 'label' => __('No')],
            ['value' => self::ALWAYS, 'label' => __('Always')],
            ['value' => self::HOVER, 'label' => __('On Hover')],
        ];
    }
}
