<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Setup\Operation;

use Amasty\Base\Helper\Deploy;
use Amasty\JetTheme\Model\DirReader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\Driver\File as FileReader;

class ProcessLocalXml
{
    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var WriteInterface
     */
    private $pubDirectory;

    /**
     * @var Deploy
     */
    private $pubDeployer;

    /**
     * @var DirReader
     */
    private $dirReader;

    public function __construct(
        FileReader $fileReader,
        Filesystem $filesystem,
        Deploy $pubDeployer,
        DirReader $dirReader
    ) {
        $this->fileReader = $fileReader;
        $this->filesystem = $filesystem;
        $this->pubDeployer = $pubDeployer;
        $this->dirReader = $dirReader;
        $this->pubDirectory = $filesystem->getDirectoryWrite(DirectoryList::PUB);
    }

    /**
     * Backup and put content to client local.xml file
     * @throws FileSystemException
     */
    public function execute(): void
    {
        $pubDirectory = $this->pubDirectory->getAbsolutePath();
        if ($this->fileReader->isExists($pubDirectory . 'errors/local.xml')) {
            try {
                $this->fileReader->copy(
                    $pubDirectory . 'errors/local.xml',
                    $pubDirectory . 'errors/local.xml_backup_by_amasty'
                );

                $fileContent = $this->fileReader->fileGetContents($pubDirectory . 'errors/local.xml');
                $changedContent = preg_replace("#<skin[^>]*>.*?</skin>#is", '<skin>jettheme</skin>', $fileContent);
                $this->fileReader->filePutContents($pubDirectory . 'errors/local.xml', $changedContent);
            } catch (\Exception $e) {
                unset($e);
            }
        }
    }
}
