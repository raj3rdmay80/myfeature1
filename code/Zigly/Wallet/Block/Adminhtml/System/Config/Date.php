<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
namespace Zigly\Wallet\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Stdlib\DateTime;
use Magento\Config\Block\System\Config\Form\Field;

class Date extends Field
{
    public function render(AbstractElement $element)
    {
        $element->setDateFormat(DateTime::DATE_INTERNAL_FORMAT);
        $element->setTimeFormat(null);
        return parent::render($element);
    }
}