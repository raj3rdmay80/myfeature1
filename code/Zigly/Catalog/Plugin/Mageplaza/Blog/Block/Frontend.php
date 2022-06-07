<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Catalog\Plugin\Mageplaza\Blog\Block;

class Frontend
{

    public function afterGetPostInfo(
        \Mageplaza\Blog\Block\Frontend $subject,
        $result,
        $post
    ) {
    	$html = '';
    	if ($categoryPost = $subject->getPostCategoryHtml($post)) {
            $html .= $categoryPost;
        }
        if ($post->getViewTraffic()) {
        	$html = ($html) ? $html.'| ' : $html;
            $html .= __(
                '<i class="mp-blog-icon mp-blog-traffic" aria-hidden="true"></i> %1',
                $post->getViewTraffic()
            );
        }
        return $html;
    }
}
