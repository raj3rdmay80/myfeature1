<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Config\Backend;

use Magento\Framework\App\Config\Data\ProcessorInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

class Color extends Value implements ProcessorInterface
{
    /**
     * @return void
     * @throws LocalizedException
     */
    public function beforeSave(): void
    {
        $value = (string)$this->getValue();
        if (!$this->validateValue($value)) {
            $fieldLabel = $this->getData('field_config')['label'] ?? '';
            throw new LocalizedException(__('%1 value must be in hex format (#AABB00)', $fieldLabel));
        }
    }

    /**
     * @param string $value
     * @return string
     */
    public function processValue($value): string
    {
        return $value;
    }

    /**
     * @param string $value
     * @return bool
     */
    private function validateValue(string $value): bool
    {
        return preg_match('/#([a-f0-9]{3}){1,2}\b/i', $value) === 1;
    }
}
