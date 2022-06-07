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
use Magento\Framework\Phrase;

class Ajax extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $url = $this->getUrl('theme/design_config');
        $element->setComment($element->getComment() . ' ' . $this->getCommentMessage($url));

        return parent::render($element);
    }

    /**
     * @param string $url
     * @return Phrase
     */
    private function getCommentMessage(string $url): Phrase
    {
        return __(
            'Please <a href="%1" target="_blank">apply JET Theme</a> to needed website/storeview' .
            ' to make this option work.',
            $url
        );
    }
}
