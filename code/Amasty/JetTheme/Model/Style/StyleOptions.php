<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Style;

use Magento\Framework\Data\OptionSourceInterface;

class StyleOptions implements OptionSourceInterface
{
    /**
     * @var FileReader
     */
    private $fileReader;

    public function __construct(FileReader $fileReader)
    {
        $this->fileReader = $fileReader;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $presets = [];
        foreach ($this->fileReader->getFileNames(FileReader::STYLE) as $fileName) {
            $fileNameParts = array_map('ucfirst', explode('_', $fileName));
            $presets[] = ['value' => $fileName, 'label' => implode(' ', $fileNameParts)];
        }

        return $presets;
    }
}
