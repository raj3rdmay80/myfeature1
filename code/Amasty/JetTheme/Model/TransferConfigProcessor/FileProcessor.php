<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\TransferConfigProcessor;

use Amasty\JetTheme\Model\DirReader;
use Magento\Framework\Filesystem\Driver\File as FileReader;
use Magento\Framework\Filter\Template;

class FileProcessor
{
    const SAMPLE_DIR = 'Less';

    /**
     * @var Template
     */
    private $templateFilter;

    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var DirReader
     */
    private $moduleDirReader;

    public function __construct(
        Template $templateFilter,
        FileReader $fileReader,
        DirReader $moduleDirReader
    ) {
        $this->templateFilter = $templateFilter;
        $this->fileReader = $fileReader;
        $this->moduleDirReader = $moduleDirReader;
    }

    /**
     * Get styles config and put it into temporary file
     *
     * @param string $templateFile
     * @return string
     */
    public function processFile(string $templateFile): string
    {
        $sampleFolderPath = $this->moduleDirReader->getSampleModuleDir(self::SAMPLE_DIR);
        $content = $this->fileReader->fileGetContents($sampleFolderPath . $templateFile);
        $content = $this->templateFilter->filter($content);

        return $content;
    }
}
