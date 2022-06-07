<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Block\Adminhtml\System\Config\Footer;

use Amasty\JetTheme\Block\Adminhtml\System\Config\Field\Footer\LayoutRenderer;
use Amasty\JetTheme\Model\Config\FooterConfig;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Layout extends Field
{
    /**
     * @var FooterConfig
     */
    private $footerConfig;

    public function __construct(
        Context $context,
        FooterConfig $footerConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->footerConfig = $footerConfig;
    }

    /**
     * @return array
     */
    protected function getLayouts(): array
    {
        $layouts = $this->footerConfig->getConfigByType('layouts');
        $config = [];
        foreach ($layouts as $layoutName => $layoutData) {
            $config[] = ['value' => $layoutName, 'label' => __($layoutData['label'])];
        }

        return $config;
    }

    /**
     * @param array $blocks
     * @return array
     */
    private function prepareConfig(array $blocks): array
    {
        foreach ($blocks as &$block) {
            if (isset($block['backend_image'])) {
                $block['backend_image'] = $this->getViewFileUrl($block['backend_image']);
            }
        }

        return $blocks;
    }

    /**
     * @return array
     */
    public function getLayoutConfig(): array
    {
        $contentBlocks = $this->footerConfig->getConfigByType('blocks');

        return [
            'content' => $this->prepareConfig($contentBlocks),
            'layouts' => $this->getLayouts(),
        ];
    }

    /**
     * @param AbstractElement $element
     * @return bool|string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $result = false;
        $renderer = $this->getLayout()->createBlock(LayoutRenderer::class);

        if ($renderer) {
            $result = $renderer->setElementId($element->getHtmlId())
                ->setElementName($element->getName())
                ->setElementValue($element->getValue())
                ->setLayoutConfig($this->getLayoutConfig())
                ->toHtml();
        }

        return $result;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html = '<td colspan="4">';
        if ($this->_isInheritCheckboxRequired($element)) {
            $inherit = $element->getInherit() == 1 ? 'checked="checked"' : '';
            if ($inherit) {
                $element->setDisabled(true);
            }
        }

        $html .= '<div class="label"><label for="'
            . $element->getHtmlId() . '"><span'
            . $this->_renderScopeLabel($element)
            . '>'
            . $element->getLabel()
            . '</span></label></div>';

        $html .= $this->_getElementHtml($element);
        $html .= '</td>';
        if ($this->_isInheritCheckboxRequired($element)) {
            $html .= $this->_renderInheritCheckbox($element);
        }

        return '<tr id="row_' . $element->getHtmlId() . '">' . $html . '</tr>';
    }
}
