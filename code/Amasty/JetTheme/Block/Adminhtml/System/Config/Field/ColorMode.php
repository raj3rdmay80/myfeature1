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

class ColorMode extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $html = '<div class="amtheme-admin-color-switcher-field" data-bind="scope:\'field_'
            . $element->getHtmlId() .
            '\'"> '
            . $element->getElementHtml()
            . '</div>';

        $value = $element->getData('value');

        $html .= '<script type="text/x-magento-init">
            {
                "*": {
                    "Magento_Ui/js/core/app": {
                        "components": {
                            "field_' . $element->getHtmlId() . '": {
                                "component": "Amasty_JetTheme/js/view/color-field",
                                "config": {
                                    "htmlId":"' . $element->getHtmlId() . '",
                                    "value":"' . $value . '",
                                    "listens": {
                                         "isInputInitialized": "setColor"
                                    },
                                    "fieldId": "' . $element->getOriginalData('id') . '"
                                }
                            }
                        }
                    }
                }
            }
        </script>';

        return $html;
    }
}
