<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


namespace Amasty\JetTheme\Model\Template\Filter;

use Amasty\JetTheme\Model\Style\FileReader;
use Magento\Framework\Filter\DirectiveProcessorInterface;
use Magento\Framework\Filter\Template;

class ColorPresetDirective implements DirectiveProcessorInterface
{
    const PATTERN = '/{{color_preset \(([a-z\_]{0,50})\)}}/si';
    /**
     * @var FileReader
     */
    private $fileReader;

    public function __construct(FileReader $fileReader)
    {
        $this->fileReader = $fileReader;
    }

    /**
     * @param array $construction
     * @param Template $filter
     * @param array $templateVariables
     * @return string
     */
    public function process(array $construction, Template $filter, array $templateVariables): string
    {
        return $this->fileReader->getFileContent($construction[1], FileReader::COLOR);
    }

    /**
     * @return string
     */
    public function getRegularExpression(): string
    {
        return self::PATTERN;
    }
}
