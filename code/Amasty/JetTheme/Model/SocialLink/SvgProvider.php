<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\SocialLink;

use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Module\Dir;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class SvgProvider
{
    /**
     * @var Dir
     */
    private $moduleDir;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var File
     */
    private $ioFile;

    /**
     * @var AssetRepository
     */
    private $assetRepo;

    public function __construct(
        Dir $moduleDir,
        DriverInterface $driver,
        File $ioFile,
        AssetRepository $assetRepo
    ) {
        $this->moduleDir = $moduleDir;
        $this->driver = $driver;
        $this->ioFile = $ioFile;
        $this->assetRepo = $assetRepo;
    }

    /**
     * @param string $key
     * @return string|null
     */
    public function getSvgContentByKey(string $key): ?string
    {
        return $this->ioFile->read($this->getSvgDir() . '/' . $key . '.svg');
    }

    /**
     * @return array
     */
    public function getAllSvg(): array
    {
        $files = [];
        foreach ($this->driver->readDirectory($this->getSvgDir()) as $file) {
            $fileContent = $this->driver->fileGetContents($file);
            $fileName = $this->ioFile->getPathInfo($file)['filename'];
            $files[$fileName] = $fileContent;

        }

        return $files;
    }

    /**
     * @param string $key
     * @return string
     */
    public function getSvgUrlByKey(string $key): string
    {
        return $this->assetRepo->getUrl('Amasty_JetTheme::svg/social/' . $key . '.svg');
    }

    /**
     * @return string
     */
    private function getSvgDir(): string
    {
        return $this->moduleDir->getDir('Amasty_JetTheme', Dir::MODULE_VIEW_DIR) . '/base/web/svg/social';
    }
}
