<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Config\Source\CategoryPage;

use Magento\Framework\Data\OptionSourceInterface;

class ProductName implements OptionSourceInterface
{
    const DEFAULT = 'default';
    const ALL_CAPS = 'uppercase';
    const ALL_LOWER_CASE = 'lowercase';
    const TITLE_CASE = 'capitalize';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::DEFAULT, 'label' => __('Default')],
            ['value' => self::ALL_CAPS, 'label' => __('All Caps')],
            ['value' => self::ALL_LOWER_CASE, 'label' => __('All Lower Case')],
            ['value' => self::TITLE_CASE, 'label' => __('Each Word with the Capital Letter')],
        ];
    }
}
