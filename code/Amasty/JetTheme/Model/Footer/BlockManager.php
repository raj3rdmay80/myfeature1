<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Footer;

use Amasty\JetTheme\Model\Config\FooterConfig;
use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Psr\Log\LoggerInterface;

class BlockManager
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var FooterConfig
     */
    private $footerConfig;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        LayoutInterface $layout,
        FooterConfig $footerConfig,
        LoggerInterface $logger,
        ConfigProvider $configProvider
    ) {
        $this->layout = $layout;
        $this->logger = $logger;
        $this->footerConfig = $footerConfig;
        $this->configProvider = $configProvider;
    }

    /**
     * @param array $blocks
     * @return string
     */
    public function getBlocksHtmlByNames(array $blocks): string
    {
        $html = '';
        $footerConfig = $this->footerConfig->getConfigByType('blocks');
        foreach ($blocks as $blockName) {
            $blockConfig = $footerConfig[$blockName] ?? null;
            if ($blockConfig === null) {
                continue;
            }

            $html .= $this->getBlockHtmlByBlockConfig($blockConfig);
        }

        return $html;
    }

    /**
     * @param array $blockConfig
     * @return string
     */
    public function getBlockHtmlByBlockConfig(array $blockConfig): string
    {
        try {
            $block = null;
            if (isset($blockConfig['config_path']) && !$this->canShowBlock($blockConfig['config_path'])) {
                return '';
            }

            if (!empty($blockConfig['name_in_layout'])) {
                $block = $this->layout->getBlock($blockConfig['name_in_layout']);
            }

            if (!$block) {
                $block = $this->layout->createBlock($blockConfig['front_end_block_type'] ?? Template::class)
                    ->setData($blockConfig);
                if (!empty($blockConfig['template'])) {
                    $block->setTemplate($blockConfig['template']);
                }
            }
            return $block ? $block->toHtml() : '';

        } catch (\Exception $e) {
            $this->logger->critical($e);
            return '';
        }
    }

    /**
     * @param string $setting
     * @return bool
     */
    private function canShowBlock(string $setting): bool
    {
        return $this->configProvider->isSetFlagSetting($setting);
    }
}
