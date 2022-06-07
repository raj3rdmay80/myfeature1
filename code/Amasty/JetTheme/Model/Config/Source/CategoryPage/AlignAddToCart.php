<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Config\Source\CategoryPage;

use Magento\Framework\Data\OptionSourceInterface;

class AlignAddToCart implements OptionSourceInterface
{
    const CENTER = 'center';
    const LEFT = 'left';
    const FULL_WIDTH = 'full-width';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::CENTER, 'label' => __('Center')],
            ['value' => self::LEFT, 'label' => __('Left')],
            ['value' => self::FULL_WIDTH, 'label' => __('Full Width')],
        ];
    }
}
