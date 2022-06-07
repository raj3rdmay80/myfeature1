<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Model\Attribute;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\Escaper;

class Attribute extends ItemsProvider
{
    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(CollectionFactory $collectionFactory, Escaper $escaper)
    {
        parent::__construct($collectionFactory);
        $this->escaper = $escaper;
    }

    /**
     * @return array
     */
    public function getVariablesOptionArray()
    {
        $collection = $this->getAttributeItems();
        $variables = [];
        foreach ($collection as $attribute) {
            $variables[] = [
                'value' => '{{amcustomtabs_attribute code="' . $attribute->getAttributeCode() . '"}}',
                'label' => __('%1', $this->escaper->escapeHtml($attribute->getFrontendLabel())),
            ];
        }
        if ($variables) {
            $variables = ['label' => __('Custom Variables'), 'value' => $variables];
        }
        return $variables;
    }
}
