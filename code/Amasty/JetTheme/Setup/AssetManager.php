<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Setup;

use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\MediaGallerySynchronization\Model\GetAssetsIterator;
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeFilesInterface;
use SplFileInfo;

class AssetManager
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ProductMetadataInterface
     */
    private $metadata;

    /**
     * @var State
     */
    private $state;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var array
     */
    private $fileExtensions;

    /**
     * @var SynchronizeFilesInterface|null
     */
    private $synchronizeFile = null;

    /**
     * @var GetAssetsIterator|null
     */
    private $assetIterator = null;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ProductMetadataInterface $metadata,
        State $state,
        Filesystem $filesystem,
        array $fileExtensions
    ) {
        $this->objectManager = $objectManager;
        $this->metadata = $metadata;
        $this->state = $state;
        $this->filesystem = $filesystem;
        $this->fileExtensions = $fileExtensions;
    }

    /**
     * @return void
     */
    private function init(): void
    {
        $this->synchronizeFile = $this->objectManager->create(SynchronizeFilesInterface::class);
        $this->assetIterator = $this->objectManager->create(GetAssetsIterator::class);
    }

    /**
     * @return void
     */
    public function synchronizeThemeMedia(): void
    {
        if (version_compare($this->metadata->getVersion(), '2.4.0', '>=')) {
            $this->init();
            $assetsPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath('amasty/jet_theme');
            $filesToProcess = [];
            $isNeedStringPath = version_compare($this->metadata->getVersion(), '2.4.1', '>=');

            /** @var SplFileInfo $file */
            foreach ($this->assetIterator->execute($assetsPath) as $file) {
                $relativePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
                    ->getRelativePath($file->getPathName());
                if (!$this->isApplicable($relativePath)) {
                    continue;
                }

                $filesToProcess[] = $isNeedStringPath ?
                    $relativePath :
                    $file;
            }

            if ($filesToProcess) {
                $this->state->emulateAreaCode(Area::AREA_ADMINHTML, function () use ($filesToProcess) {
                    $this->synchronizeFile->execute($filesToProcess);
                });
            }
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    private function isApplicable(string $path): bool
    {
        return $path && preg_match('#\.(' . implode("|", $this->fileExtensions) . ')$# i', $path);
    }
}
