<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Style\Color;

use Amasty\JetTheme\Model\Style\FileReader;
use Magento\Framework\Data\OptionSourceInterface;

class PresetOptions implements OptionSourceInterface
{
    const DEFAULT_COLOR_PRESET = 'default';

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
        foreach ($this->fileReader->getFileNames(FileReader::COLOR) as $fileName) {
            if ($fileName == self::DEFAULT_COLOR_PRESET) {
                continue;
            }

            $fileNameParts = array_map('ucfirst', explode('_', $fileName));
            $presets[] = ['value' => $fileName, 'label' => implode(' ', $fileNameParts)];
        }

        $presets = array_merge(
            [
                ['value' => self::DEFAULT_COLOR_PRESET, 'label' => 'Default']
            ],
            $presets
        );

        return $presets;
    }
}
