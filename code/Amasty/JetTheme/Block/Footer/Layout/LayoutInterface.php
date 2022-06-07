<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Block\Footer\Layout;

interface LayoutInterface
{
    /**
     * @param array $layoutConfig
     */
    public function setLayoutConfig(array $layoutConfig): LayoutInterface;
}
