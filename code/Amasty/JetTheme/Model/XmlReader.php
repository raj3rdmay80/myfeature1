<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Driver\File as FileReader;
use Magento\Framework\Xml\Parser as XmlParser;

class XmlReader
{
    const SAMPLE_DIR = 'Config';

    /**
     * @var DirReader
     */
    private $moduleDirReader;

    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var XmlParser
     */
    private $xmlParser;

    public function __construct(
        DirReader $moduleDirReader,
        XmlParser $xmlParser,
        FileReader $fileReader
    ) {
        $this->moduleDirReader = $moduleDirReader;
        $this->xmlParser = $xmlParser;
        $this->fileReader = $fileReader;
    }

    /**
     * @param string $xmlFileName
     * @return null|array
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function readXmlFile($xmlFileName): ?array
    {
        $configFolderPath = $this->moduleDirReader->getSampleModuleDir(self::SAMPLE_DIR);
        $xmlPath = $configFolderPath . '/' . $xmlFileName;
        if (!$this->fileReader->isExists($xmlPath)) {
            return null;
        }

        $xmlContent = $this->fileReader->fileGetContents($xmlPath);
        try {
            $xmlArray = $this->xmlParser->loadXML($xmlContent)->xmlToArray();
        } catch (LocalizedException $e) {
            throw new LocalizedException(
                new \Magento\Framework\Phrase(
                    'Invalid Document: %1%2 Error: %3',
                    [$xmlPath, PHP_EOL, $e->getMessage()]
                ),
                $e
            );
        }

        return $xmlArray;
    }
}
