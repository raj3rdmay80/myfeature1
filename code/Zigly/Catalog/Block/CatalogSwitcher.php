<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Catalog
 */
declare(strict_types=1);

namespace Zigly\Catalog\Block;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;

class CatalogSwitcher extends \Magento\Framework\View\Element\Template
{
    /**
     * @var categoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @param Context $context
     * @param Resolver $layerResolver
     * @param Registry $registry
     * @param Category $categoryHelper
     * @param CollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * get current category
     * @return void
     */
    public function getCurrentCategory()
    {
        return $this->coreRegistry->registry('current_category');
    }

    /**
     * get catalog switchers collection
     * @return void
     */
    public function getSwitchersCollection()
    {
        $level= $this->getCurrentCategory()->getLevel();
        $categories = $this->categoryCollectionFactory->create();
        $categories->addAttributeToSelect('*');
        $categories->addAttributeToFilter('level' , $level);
        $categories->addAttributeToFilter('parent_id' , $this->getCurrentCategory()->getParentId()); 
        $categories->addAttributeToFilter('is_active' , 1);
        $categories->addAttributeToFilter('include_in_menu' , 1);
        $categories->addAttributeToFilter('include_in_switcher' , 1);
        return $categories;
    }
}