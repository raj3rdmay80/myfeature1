<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model;

class AttributeLoader
{
    /**
     * @param array $xmlArray
     * @param string $globalTagName
     * @return array
     */
    public function loadAttributes(array $xmlArray, string $globalTagName): array
    {
        $resultAttributes = [];
        $attributesData = [];
        foreach ($xmlArray['config']['_value'][$globalTagName] as $attributes) {
            foreach ($attributes as $attribute) {
                $attribute = array_key_exists('item', $attribute) ? $attribute['item'] : $attribute;
                foreach ($attribute as $itemData) {
                    $attributesData[$itemData['_attribute']['name']] = $itemData['_value'];
                }
                $resultAttributes[$attributesData['identifier']] = $attributesData;
            }
        }

        return $resultAttributes;
    }
}
