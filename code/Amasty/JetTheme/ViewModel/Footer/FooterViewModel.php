<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel\Footer;

use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Cms\Block\Block;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Layout;
use Psr\Log\LoggerInterface;

class FooterViewModel implements ArgumentInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Layout
     */
    private $layout;

    public function __construct(
        ConfigProvider $configProvider,
        Layout $layout,
        LoggerInterface $logger
    ) {
        $this->configProvider = $configProvider;
        $this->logger = $logger;
        $this->layout = $layout;
    }

    /**
     * @return bool
     */
    public function isLogoBlockEnabled(): bool
    {
        return $this->configProvider->isLogoBlockEnabled();
    }

    /**
     * @return bool
     */
    public function isSubscriptionBlockEnabled(): bool
    {
        return $this->configProvider->isSubscriptionBlockEnabled();
    }

    /**
     * @return string
     */
    public function getPlaceholderEmail()
    {
        return $this->configProvider->getPlaceholderEmail();
    }

    /**
     * @return bool
     */
    public function isSubscriptionBlockMobileEnabled(): bool
    {
        return $this->configProvider->isSubscriptionBlockMobileEnabled();
    }

    /**
     * @return bool
     */
    public function isCustomMiniFooterEnabled(): bool
    {
        return $this->configProvider->isCustomMiniFooterEnabled();
    }

    /**
     * @return string
     */
    public function getCustomMiniFooterContent(): string
    {
        try {
            $blockId = $this->configProvider->getCustomMiniFooterCmsBlockId();

            return $this->getBlockContent($blockId);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return '';
    }

    /**
     * @return string
     */
    public function getCustomCmsBlockContent(): string
    {
        try {
            $blockId = $this->configProvider->getCustomCmsBlockId();

            return $this->getBlockContent($blockId);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return '';
    }

    /**
     * @param int $blockId
     * @return string
     */
    private function getBlockContent(int $blockId): string
    {
        if (!$blockId) {
            return '';
        }

        try {
            return $this->layout->createBlock(Block::class)->setBlockId($blockId)->toHtml();
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return '';
    }

    /**
     * @return string
     */
    public function getAlignPaymentMethodsBlock(): string
    {
        return $this->configProvider->getAlignPaymentMethodsBlock();
    }
}
