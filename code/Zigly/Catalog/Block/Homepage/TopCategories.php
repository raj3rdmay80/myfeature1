<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Catalog\Block\Homepage;

class TopCategories extends \Magento\Framework\View\Element\Template
{
    
    protected $_categoryCollection;
    
    protected $_storeManager;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        array $data = []
    ) {
        $this->_categoryCollection = $categoryCollection;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * @return object
     */
    public function getCategories()
    {
        $collection = $this->_categoryCollection->create()
            ->addAttributeToSelect('*')
            ->setStore($this->_storeManager->getStore())
            ->addAttributeToFilter('is_top_category', 1)
            ->setPageSize(6)
            ->setCurPage(1);
        return $collection;
    }
}
