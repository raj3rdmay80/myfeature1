<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Plugin\Framework\Css\PreProcessor\File;

use Amasty\JetTheme\Model\ConfigProvider;
use Amasty\JetTheme\Model\StoreThemeMapper;
use Amasty\JetTheme\Model\TransferConfigProcessor\Color;
use Amasty\JetTheme\Model\TransferConfigProcessor\Font;
use Amasty\JetTheme\Model\TransferConfigProcessor\TransferConfigInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Css\PreProcessor\File\Temporary;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class TemporaryPlugin for transfer config values to static content deployed less file
 */
class TemporaryPlugin
{
    const PATH_TO_COLOR_VARS = 'css/source/variables/';
    const PATH_TO_FONT_VARS = 'css/source/';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Color
     */
    private $transferColorConfigProcessor;

    /**
     * @var Font
     */
    private $transferFontConfigProcessor;

    /**
     * @var Emulation
     */
    private $emulation;

    /**
     * @var StoreThemeMapper
     */
    private $storeThemeMapper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        ConfigProvider $configProvider,
        Color $transferColorConfigProcessor,
        Font $transferFontConfigProcessor,
        Emulation $emulation,
        StoreThemeMapper $storeThemeMapper,
        StoreManagerInterface $storeManager
    ) {
        $this->configProvider = $configProvider;
        $this->transferColorConfigProcessor = $transferColorConfigProcessor;
        $this->transferFontConfigProcessor = $transferFontConfigProcessor;
        $this->emulation = $emulation;
        $this->storeThemeMapper = $storeThemeMapper;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Temporary $subject
     * @param string $relativePath
     * @param string $contents
     * @return string[]
     */
    public function beforeCreateFile(Temporary $subject, $relativePath, $contents): array
    {
        $storeId = $this->storeThemeMapper->getStoreIdByThemeFilePath($relativePath);
        if (strpos($relativePath, "Amasty") !== false
            && strpos($relativePath, self::PATH_TO_COLOR_VARS . Color::OUTPUT_LESS_FILE) !== false
            && $this->configProvider->isCustomColorsEnabled($storeId)
        ) {
            $contents = $this->getFileContent($this->transferColorConfigProcessor, $storeId);
        }

        if (strpos($relativePath, "Amasty") !== false
            && strpos($relativePath, self::PATH_TO_FONT_VARS . Font::OUTPUT_LESS_FILE) !== false
            && $this->configProvider->isGoogleFontsEnabled($storeId)
        ) {
            $contents = $this->getFileContent($this->transferFontConfigProcessor, $storeId);
        }

        return [$relativePath, $contents];
    }

    /**
     * @param TransferConfigInterface $transferConfigProcessor
     * @param string $storeId
     * @return string
     */
    private function getFileContent($transferConfigProcessor, $storeId): string
    {
        $this->configProvider->clean();
        $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND);
        $content = $transferConfigProcessor->process();
        $this->emulation->stopEnvironmentEmulation();

        return $content;
    }
}
