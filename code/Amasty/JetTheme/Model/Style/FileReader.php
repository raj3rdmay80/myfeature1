<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Style;

use Amasty\JetTheme\Model\DirReader;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\Io\File as IoFile;

class FileReader
{
    const COLOR = 1;
    const STYLE = 2;

    /**
     * @var DirReader
     */
    private $dirReader;

    /**
     * @var FileDriver
     */
    private $fileDriver;

    /**
     * @var IoFile
     */
    private $ioFile;

    public function __construct(
        DirReader $dirReader,
        FileDriver $fileDriver,
        IoFile $ioFile
    ) {
        $this->dirReader = $dirReader;
        $this->fileDriver = $fileDriver;
        $this->ioFile = $ioFile;
    }

    /**
     * @param int $type
     * @return array
     */
    public function getFileNames(int $type = self::COLOR): array
    {
        $files = $this->fileDriver->readDirectory($this->getDirByType($type));
        $resultFiles = [];
        foreach ($files as $file) {
            if ($this->fileDriver->isFile($file)) {
                $resultFiles[] = $this->ioFile->getPathinfo($file)['filename'];
            }
        }

        return $resultFiles;
    }

    /**
     * @param string $fileName
     * @param int $type
     * @return string
     */
    public function getFileContent(string $fileName, int $type = self::COLOR): string
    {
        $dir = $this->getDirByType($type);

        return $dir ? $this->fileDriver->fileGetContents($dir . $fileName . '.json') : '';
    }

    /**
     * @param int $type
     * @return string
     */
    private function getDirByType(int $type): string
    {
        $dir = '';
        switch ($type) {
            case self::COLOR:
                $dir = $this->dirReader->getColorPresetsDir();
                break;
            case self::STYLE:
                $dir = $this->dirReader->getStylePresetsDir();
                break;
            default:
                break;
        }

        return $dir;
    }
}
