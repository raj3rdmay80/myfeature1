<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel\Checkout;

use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class MinicartViewModel implements ArgumentInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @return bool
     */
    public function isOpenMinicart(): bool
    {
        return $this->configProvider->isOpenMinicart();
    }

    /**
     * @return bool
     */
    public function isStickyMinicartEnabled(): bool
    {
        return $this->configProvider->isStickyMinicartEnabled();
    }
}
