<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Config\Source\Newsletter;

use Magento\Framework\Data\OptionSourceInterface;

class TextPosition implements OptionSourceInterface
{
    const INLINE = 'inline';
    const BEFORE_INPUT = 'before-input';
    const AFTER_INPUT = 'after-input';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::INLINE, 'label' => __('Inline')],
            ['value' => self::BEFORE_INPUT, 'label' => __('Before Input')],
            ['value' => self::AFTER_INPUT, 'label' => __('After Input')],
        ];
    }
}
