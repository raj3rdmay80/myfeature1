<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Block\Adminhtml\System\Config\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class StyleSwitcher extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        return $element->getElementHtml() . $this->toHtml();
    }

    /**
     * @return string
     */
    protected function _toHtml(): string
    {
        $this->setTemplate('Amasty_JetTheme::style/buttons/style_switcher.phtml');

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getPresetDataUrl(): string
    {
        return $this->_urlBuilder->getUrl('amasty_jettheme/styleSwitcher/styleData');
    }
}
