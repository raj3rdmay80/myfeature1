<?php
/**
 * @author Zigly Team
 * @copyright Copyright (c) 2021 Zigly
 * @package Zigly_ReviewTag
 */

namespace Zigly\ReviewTag\Plugin\Review\Model;

use Magento\Review\Model\Review as MagentoReview;
use Magento\Framework\App\RequestInterface;

class Review
{
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }
    /**
     * @param MagentoReview $subject
     */
    public function beforeSave(MagentoReview $subject)
    {
        $tagName = $this->request->getPostValue('tag_name');
        if (!empty($tagName)) {
            $subject->setData('feedback_tag', implode(", ", $tagName));
        }
    }
}
