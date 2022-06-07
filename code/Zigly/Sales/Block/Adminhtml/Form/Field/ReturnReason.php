<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
declare(strict_types=1);

namespace Zigly\Sales\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class ReturnReason
 */
class ReturnReason extends AbstractFieldArray
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn('returnreason', ['label' => __('Reason for Return Order'), 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Reason');
    }
}
