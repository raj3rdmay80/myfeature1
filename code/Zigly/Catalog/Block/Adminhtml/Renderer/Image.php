<?php

namespace Zigly\Catalog\Block\Adminhtml\Renderer;

/**
 * Renderer image for admin form
 * Add media path to url
 *
 * Class Image
 * @package Mageplaza\Core\Block\Adminhtml\Renderer
 */
class Image extends \Mageplaza\Core\Block\Adminhtml\Renderer\Image
{
    /**
     * @return string
     */
    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($this->getValue()) {
            $html .= $this->_getHiddenInput();
        }
        $html .= '<style>#author_image_image, #post_image_image{ position: relative;top: 6px }</style>';

        return $html;
    }
}
