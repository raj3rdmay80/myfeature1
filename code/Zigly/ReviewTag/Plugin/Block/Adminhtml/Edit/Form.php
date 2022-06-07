<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Plugin\Block\Adminhtml\Edit;

class Form extends \Magento\Review\Block\Adminhtml\Edit\Form
{
    public function beforeSetForm(\Magento\Review\Block\Adminhtml\Edit\Form $object, $form) {

        $review = $object->_coreRegistry->registry('review_data');

        $fieldset = $form->getElement('review_details');
        $fieldset->removeField('detailed-rating');
        $fieldset->addField(
            'feedback_tag',
            'note',
            [
                'label'    => __('Feedback Tag'),
                'required' => false,
                'name'     => 'feedback_tag',
                'text'     => $review->getFeedbackTag() ? $review->getFeedbackTag() : __(''),
            ]
        );
        $form->setValues($review->getData());
        return [$form];
    }
}