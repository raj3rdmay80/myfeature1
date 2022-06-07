<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Blog
 */
declare(strict_types=1);

namespace Zigly\Blog\Block\Homepage;

use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Blog\Model\ResourceModel\Post\CollectionFactory as BlogPostCollection;

class Blogs extends \Magento\Framework\View\Element\Template
{
    /**
     * @var BlogPostCollection
     */
    protected $blogPostCollection;

    /**
     * @param Context $context
     * @param BlogPostCollection $blogPostCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        BlogPostCollection $blogPostCollection
    ) {
        $this->blogPostCollection = $blogPostCollection;
        parent::__construct($context);
    }

    /**
     * Get Blogs
     * @return string
     */
    public function getBlog()
    {
        $blogs = $this->blogPostCollection->create()->join(
            ['category' => 'mageplaza_blog_post_category'],
            'category.post_id = main_table.post_id and category.category_id = 4',
            'GROUP_CONCAT(category.category_id SEPARATOR \',\') as category'
        );
        $blogs->getSelect()->group('main_table.post_id')->limit(3);
        return $blogs;
    }
}
