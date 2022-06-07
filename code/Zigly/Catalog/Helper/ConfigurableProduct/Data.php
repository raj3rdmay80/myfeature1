<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Catalog
 */

namespace Zigly\Catalog\Helper\ConfigurableProduct;

class Data extends \Nordcomputer\Showoutofstockprice\ConfigurableProduct\Helper\Data\Data
{
    public function getOptions($currentProduct, $allowedProducts)
    {
        $options = [];
        $allowAttributes = $this->getAllowAttributes($currentProduct);

        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            foreach ($allowAttributes as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                if ($product->isSalable()) {
                    $options[$productAttributeId][$attributeValue][] = $productId;
                }
                $options['index'][$productId][$productAttributeId] = $attributeValue;
            }
        }
        return $options;
    }
}
