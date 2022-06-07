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

class PaymentLinks extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $url = $this->getUrl('amasty_jettheme/paymentlink/index');
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
            'You may configure you Payment Methods Icons <a href="%1" target="_blank">here</a>',
            $url
        );
    }
}
