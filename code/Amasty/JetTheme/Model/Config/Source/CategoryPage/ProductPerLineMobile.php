<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Config\Source\CategoryPage;

use Magento\Framework\Data\OptionSourceInterface;

class ProductPerLineMobile implements OptionSourceInterface
{
    const ONE = 1;
    const TWO = 2;

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::ONE, 'label' => 1],
            ['value' => self::TWO, 'label' => 2],
        ];
    }
}
