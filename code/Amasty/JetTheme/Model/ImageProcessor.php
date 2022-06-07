<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model;

use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Image;
use Magento\Framework\ImageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class ImageProcessor
{
    const MEDIA_PATH = 'amasty/jet_theme';
    const MEDIA_TMP_PATH = 'amasty/jet_theme/tmp';

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var File
     */
    private $ioFile;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Filesystem $filesystem,
        ImageUploader $imageUploader,
        ImageFactory $imageFactory,
        File $ioFile,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->imageUploader = $imageUploader;
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
        $this->ioFile = $ioFile;
        $this->logger = $logger;
    }

    /**
     * @param string $filePath
     * @param string $resizedFilePath
     * @param int $width
     * @param int $height
     * @throws LocalizedException
     * @return void
     */
    public function resizeImage(string $filePath, string $resizedFilePath, int $width, int $height): void
    {
        if (!$filePath || !$this->ioFile->fileExists($filePath)) {
            throw new LocalizedException(__('File is not exist!'));
        }

        /** @var Image $imageResize */
        $imageResize = $this->imageFactory->create(['fileName' => $filePath]);
        $imageResize->open();
        if ($imageResize->getOriginalHeight() > $height && $imageResize->getOriginalWidth() > $width) {
            $imageResize->backgroundColor([255, 255, 255]);
            $imageResize->constrainOnly(true);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(true);
            $imageResize->keepAspectRatio(true);

            $imageResize->resize($width, $height);
        }

        $imageResize->save($resizedFilePath);
    }

    /**
     * @return string
     */
    public function getBaseMediaPath(): string
    {
        return self::MEDIA_PATH;
    }

    /**
     * @return WriteInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getMediaDirectory(): WriteInterface
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }

    /**
     * @param string $iconName
     *
     * @return string
     */
    private function getImageRelativePath(string $iconName): string
    {
        return self::MEDIA_PATH . DIRECTORY_SEPARATOR . $iconName;
    }

    /**
     * @param string $iconName
     * @return string
     * @throws LocalizedException
     */
    public function saveImage(string $iconName): string
    {
        try {
            $iconNameForSave = $this->prepareIconName($iconName);
            $path = $this->imageUploader->moveFileFromTmp($iconNameForSave, true);

            $filename = $this->getMediaDirectory()->getAbsolutePath($path);

            $imageProcessor = $this->imageFactory->create(['fileName' => $filename]);
            $imageProcessor->keepAspectRatio(true);
            $imageProcessor->keepFrame(true);
            $imageProcessor->keepTransparency(true);
            $imageProcessor->backgroundColor([255, 255, 255]);
            $imageProcessor->save();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }

        return $this->getImageRelativePath($iconName);
    }

    /**
     * @param string $iconName
     * @return string
     */
    private function prepareIconName(string $iconName): string
    {
        if (stripos($iconName, '/') !== false) {
            $pathParts = explode('/', $iconName);
            $this->imageUploader->setBasePath($this->imageUploader->getBasePath() . '/' . $pathParts[0]);

            return $pathParts[1];
        }

        return $iconName;
    }
}
