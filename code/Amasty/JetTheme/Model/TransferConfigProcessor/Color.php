<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\TransferConfigProcessor;

class Color implements TransferConfigInterface
{
    const OUTPUT_LESS_FILE = '_colors-system.less';
    const TEMPLATE_FILE = '_colors-system.template';

    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    public function __construct(FileProcessor $fileProcessor)
    {
        $this->fileProcessor = $fileProcessor;
    }

    /**
     * Process styles config
     *
     * @return string
     */
    public function process(): string
    {
        return $this->fileProcessor->processFile(self::TEMPLATE_FILE);
    }
}
