<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Config\Source\ProductPage;

use Magento\Framework\Data\OptionSourceInterface;

class AddToCartPosition implements OptionSourceInterface
{
    const ON_LINE = 'on-line';
    const UNDER = 'under';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::ON_LINE, 'label' => __('On the line with the quantity selector')],
            ['value' => self::UNDER, 'label' => __('Under the quantity selector')],
        ];
    }
}
