<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Observer\Framework\View\Layout;

use Amasty\JetTheme\Model\ConfigProvider;
use Amasty\JetTheme\Model\FontManager;
use Amasty\JetTheme\Model\StoreThemeMapper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Asset\Repository as AssetRepo;
use Magento\Framework\View\Page\Config as PageConfig;

class Builder implements ObserverInterface
{
    const DEFAULT_FONT_PATH = 'fonts/roboto/';
    const DEFAULT_FONT_NAME_BOLD = 'bold/Roboto-700.woff2';
    const DEFAULT_FONT_NAME_REGULAR = 'regular/Roboto-400.woff2';
    const DEFAULT_FONT_ATTRIBUTES = [
        'rel' => 'preload',
        'as' => 'font',
        'type' => 'font/woff2',
        'crossorigin' => 'anonymous'
    ];
    const ICON_FONT = 'fonts/Blank-Theme-Icons/Blank-Theme-Icons.woff2';
    const GSTATIC_FONT_URL = 'https://fonts.gstatic.com/';

    /**
     * @var PageConfig
     */
    private $pageConfig;

    /**
     * @var AssetRepo
     */
    private $assetRepo;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var FontManager
     */
    private $fontManager;

    /**
     * @var StoreThemeMapper
     */
    private $storeThemeMapper;

    public function __construct(
        PageConfig $pageConfig,
        AssetRepo $assetRepo,
        ConfigProvider $configProvider,
        FontManager $fontManager,
        StoreThemeMapper $storeThemeMapper
    ) {
        $this->pageConfig = $pageConfig;
        $this->assetRepo = $assetRepo;
        $this->configProvider = $configProvider;
        $this->fontManager = $fontManager;
        $this->storeThemeMapper = $storeThemeMapper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if (!$this->storeThemeMapper->isCurrentThemeJetTheme()) {
            return;
        }

        $this->pageConfig->addRemotePageAsset(
            $this->getFontUrl(self::ICON_FONT),
            'link_rel',
            ['attributes' => self::DEFAULT_FONT_ATTRIBUTES]
        );

        if (!$this->configProvider->isGoogleFontsEnabled()) {
            $this->processDefaultFonts();

            return;
        }

        $this->processGoogleFonts();
    }

    /**
     * @return void
     */
    private function processGoogleFonts(): void
    {
        $googleFontSetting = $this->configProvider->getGoogleFontSetting();
        $this->pageConfig->addRemotePageAsset(
            self::GSTATIC_FONT_URL,
            'link_rel',
            ['attributes' => ['rel' => 'preconnect', 'crossorigin' => '']]
        );

        $this->pageConfig->addRemotePageAsset(
            $this->fontManager->getFontUrl($googleFontSetting),
            'link_rel',
            ['attributes' => ['rel' => 'preload', 'as' => 'style']]
        );

        $this->pageConfig->addRemotePageAsset(
            $this->fontManager->getFontUrl($googleFontSetting),
            'link_rel',
            ['attributes' => ['rel' => 'stylesheet']]
        );
    }

    /**
     * @return void
     */
    private function processDefaultFonts(): void
    {
        $preloadedFonts = [
            'bold_path' => self::DEFAULT_FONT_PATH . self::DEFAULT_FONT_NAME_BOLD,
            'regular_path' => self::DEFAULT_FONT_PATH . self::DEFAULT_FONT_NAME_REGULAR,
        ];

        foreach ($preloadedFonts as $font) {
            $this->pageConfig->addRemotePageAsset(
                $this->getFontUrl($font),
                'link_rel',
                ['attributes' => self::DEFAULT_FONT_ATTRIBUTES]
            );
        }
    }

    /**
     * @param string $font
     * @return string
     */
    private function getFontUrl(string $font): string
    {
        return $this->assetRepo->getUrl($font);
    }
}
