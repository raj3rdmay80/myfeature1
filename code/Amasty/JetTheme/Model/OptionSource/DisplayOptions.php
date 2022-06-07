<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class DisplayOptions implements OptionSourceInterface
{
    const HOVER = 'hover';
    const ALWAYS = 'always';

    /**
     * @var array|null
     */
    private $options;

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        if (!$this->options) {
            $this->options = [
                ['value' => self::HOVER, 'label' => __('On Hover')],
                ['value' => self::ALWAYS, 'label' => __('Always')],
            ];
        }

        return $this->options;
    }
}
