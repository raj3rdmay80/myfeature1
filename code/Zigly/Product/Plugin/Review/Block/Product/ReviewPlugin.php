<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Product
 */

namespace Zigly\Product\Plugin\Review\Block\Product;

/**
 * Class ReviewPlugin
 */
class ReviewPlugin
{
    /**
     * @param \Magento\Review\Block\Product\Review $subject
     * @param $html
     *
     * @return string
     */
    public function aroundSetTabTitle(\Magento\Review\Block\Product\Review $subject, callable $proceed)
    {
        $title = $subject->getCollectionSize()
            ? __('Reviews (%1)', '<span class="review-counter">' . $subject->getCollectionSize() . '</span>')
            : __('Reviews');
        $subject->setTitle($title);
    }
}
