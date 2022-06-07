<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\ImageOptimizer;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Api\Data\ImageSettingInterfaceFactory;
use Amasty\ImageOptimizer\Model\ConfigProvider;
use Amasty\PageSpeedTools\Model\OptionSource\Resolutions;

class ImageSettingsGenerator
{
    const FOLDERS_TO_PROCESS = ['catalog', 'wysiwyg'];

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ImageSettingInterfaceFactory
     */
    private $imageSettingFactory;

    public function __construct(
        ConfigProvider $configProvider,
        ImageSettingInterfaceFactory $imageSettingFactory
    ) {
        $this->configProvider = $configProvider;
        $this->imageSettingFactory = $imageSettingFactory;
    }

    /**
     * @return ImageSettingInterface
     */
    public function getSettingsToProcess(): ImageSettingInterface
    {
        $imageSetting = $this->imageSettingFactory->create();
        $imageSetting->setFolders(self::FOLDERS_TO_PROCESS);
        $imageSetting->setJpegTool($this->configProvider->getJpegCommand());
        $imageSetting->setPngTool($this->configProvider->getPngCommand());
        $imageSetting->setGifTool($this->configProvider->getGifCommand());
        $imageSetting->setWebpTool($this->configProvider->getWebpCommand());
        $imageSetting->setIsDumpOriginal($this->configProvider->isDumpOriginal());
        $resolutions = $this->configProvider->getResolutions();
        if (in_array(Resolutions::MOBILE, $resolutions)) {
            $imageSetting->setIsCreateMobileResolution(true);
        }

        if (in_array(Resolutions::TABLET, $resolutions)) {
            $imageSetting->setIsCreateTabletResolution(true);
        }

        $imageSetting->setResizeAlgorithm($this->configProvider->getResizeAlgorithm());

        return $imageSetting;
    }
}
