<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Block\Footer;

use Amasty\JetTheme\Model\Config\FooterConfig;
use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;

class Layout extends Template
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var FooterConfig
     */
    private $footerConfig;

    public function __construct(
        ConfigProvider $configProvider,
        SerializerInterface $serializer,
        FooterConfig $footerConfig
    ) {
        $this->configProvider = $configProvider;
        $this->serializer = $serializer;
        $this->footerConfig = $footerConfig;
    }

    /**
     * @return string
     */
    public function toHtml(): string
    {
        if (!$this->configProvider->isFooterCustomLayout() || !$this->configProvider->getCustomLayoutValue()) {
            return '';
        }

        $layoutConfig = $this->serializer->unserialize($this->configProvider->getCustomLayoutValue());
        $footerConfig = $this->footerConfig->getConfig();
        $footerHtml = $this->getLayout()
            ->createBlock($footerConfig['layouts'][$layoutConfig['active-layout']]['layout_block'])
            ->setLayoutConfig($layoutConfig[$layoutConfig['active-layout']])
            ->toHtml();

        return $footerHtml;
    }
}
