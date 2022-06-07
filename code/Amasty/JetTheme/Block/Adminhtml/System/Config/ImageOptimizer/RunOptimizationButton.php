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

class RunOptimizationButton extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->setTemplate('Amasty_JetTheme::imageOptimizer/progressBar.phtml')->_toHtml();
    }

    /**
     * @return string
     */
    public function getStartUrl(): string
    {
        return $this->_urlBuilder->getUrl('amasty_jettheme/imageOptimizer/start');
    }

    /**
     * @return string
     */
    public function getProcessUrl(): string
    {
        return $this->_urlBuilder->getUrl('amasty_jettheme/imageOptimizer/process');
    }
}
