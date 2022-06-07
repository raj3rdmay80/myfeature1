<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel\ProductCaret;

use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductCaretConfigurationViewModel implements ArgumentInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @return bool
     */
    public function isProductCaretEnabled(): bool
    {
        return $this->configProvider->isStickAddToCartEnabled();
    }
}
