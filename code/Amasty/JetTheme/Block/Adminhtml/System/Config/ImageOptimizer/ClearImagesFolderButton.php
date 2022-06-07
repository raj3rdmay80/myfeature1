<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Block\Adminhtml\System\Config\ImageOptimizer;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ClearImagesFolderButton extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $element->setData('value', __("Clear Generated Image Folders"));
        $element->setData('class', "action-default amoptimizer-btn");
        $element->setData('onclick', "location.href = '" . $this->getActionUrl() . "'");

        return parent::_getElementHtml($element);
    }

    /**
     * @return string
     */
    public function getActionUrl(): string
    {
        return $this->_urlBuilder->getUrl('amasty_jettheme/imageOptimizer/clearImagesFolder');
    }
}
