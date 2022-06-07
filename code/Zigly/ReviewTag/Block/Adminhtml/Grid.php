<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Block\Adminhtml;

/**
 * Review
 */
class Grid extends \Magento\Review\Block\Adminhtml\Grid
{

    /**
     * Prepare grid columns
     *
     * @return \Magento\Backend\Block\Widget\Grid
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn(
            'feedback_tag',
            [
                'header' => __('Feedback Tag'),
                'filter_index' => 'rdt.feedback_tag',
                'index' => 'feedback_tag',
                'type' => 'text',
                'escape' => true,
                'filter' => false,
                'sortable' => false,
                'header_css_class' => 'col-feedback_tag',
                'column_css_class' => 'col-feedback_tag'
            ]
        );
        return $this;
    }
}