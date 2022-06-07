<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class ImageProvider
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ReadInterface
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

    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    public function __construct(
        Filesystem $filesystem,
        File $ioFile,
        StoreManagerInterface $storeManager,
        ImageProcessor $imageProcessor,
        LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->ioFile = $ioFile;
        $this->logger = $logger;
        $this->imageProcessor = $imageProcessor;
    }

    /**
     * @param string $src
     * @param int $width
     * @param int $height
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getResizedUrl(
        string $src,
        int $width = 64,
        int $height = 64
    ): string {
        try {
            $filePath = $this->getAbsolutePath($src);
            $fileName = $this->ioFile->getPathInfo($filePath)['basename'];
            $resizedFilePath = $this->getMediaDirectory()
                ->getAbsolutePath(
                    $this->imageProcessor->getBaseMediaPath() . $this->getNewDirectoryImage($fileName, $width, $height)
                );
            if (!$this->ioFile->fileExists($resizedFilePath)) {
                $this->imageProcessor->resizeImage($filePath, $resizedFilePath, $width, $height);
            }

            $resizedURL = $this->getMediaUrl(
                $this->imageProcessor->getBaseMediaPath() . $this->getNewDirectoryImage($src, $width, $height)
            );
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
            return '';
        }

        return $resizedURL;
    }

    /**
     * @param string $path
     * @return string
     */
    private function getMediaUrl(string $path): string
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $path;
    }

    /**
     * @param string $dir
     *
     * @return string
     */
    private function getAbsolutePath(string $dir): string
    {
        $path = '';
        if ($dir) {
            $path = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath($dir);
        }

        return  $path;
    }

    /**
     * @param string $imageName
     * @return string
     */
    public function getThumbnailUrl(string $imageName): string
    {
        try {
            return $this->getImageMediaUrl() . '/' . $imageName;
        } catch (NoSuchEntityException $exception) {
            return '';
        }
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    private function getImageMediaUrl(): string
    {
        return $this->storeManager->getStore()
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param string $src
     * @param int $width
     * @param int $height
     * @return string
     */
    private function getNewDirectoryImage(string $src, int $width, int $height): string
    {
        $segments = array_reverse(explode('/', $src));
        $firstDir = substr($segments[0], 0, 1);
        $secondDir = substr($segments[0], 1, 1);

        return '/cache/' . $firstDir . '/' . $secondDir . '/' . $width . '/' . $height . '/' . $segments[0];
    }

    /**
     * @return ReadInterface
     */
    private function getMediaDirectory(): ReadInterface
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }
}
