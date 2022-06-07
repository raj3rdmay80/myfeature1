<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
declare(strict_types=1);

namespace Zigly\Sales\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class CancelReason
 */
class CancelReason extends AbstractFieldArray
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn('cancelreason', ['label' => __('Reason for Cancel Order'), 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Reason');
    }
}
