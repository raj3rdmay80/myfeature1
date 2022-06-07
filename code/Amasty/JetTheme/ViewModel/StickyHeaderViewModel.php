<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel;

use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class StickyHeaderViewModel implements ArgumentInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        ConfigProvider $configProvider,
        Manager $moduleManager
    ) {
        $this->configProvider = $configProvider;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return bool
     */
    public function isStickyHeaderEnabled(): bool
    {
        return $this->configProvider->isStickyHeaderEnabled()
            && !$this->moduleManager->isEnabled('Amasty_MegaMenu');
    }

    /**
     * @return bool
     */
    public function isAlwaysDisplayStickyHeader(): bool
    {
        return $this->configProvider->isAlwaysDisplayStickyHeader();
    }

    /**
     * @return bool
     */
    public function isDisplayOnScrollDown(): bool
    {
        return $this->configProvider->isDisplayOnScrollDown();
    }

    /**
     * @return string
     */
    public function hideStickyHeaderOnScrollDownValue()
    {
        return $this->configProvider->hideStickyHeaderOnScrollDownValue();
    }

    /**
     * @return bool
     */
    public function isDisplayOnScrollUp(): bool
    {
        return $this->configProvider->isDisplayOnScrollUp();
    }
}
