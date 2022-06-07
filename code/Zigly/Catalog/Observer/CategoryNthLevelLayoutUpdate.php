<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Catalog
 */
namespace Zigly\Catalog\Observer;

use Magento\Framework\Registry;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class CategoryNthLevelLayoutUpdate implements ObserverInterface
{
    const ACTION_NAME = 'catalog_category_view';

    /** @var Registry */
    private $registry;

    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function execute(Observer $observer)
    {
        return;
        if ($observer->getFullActionName() !== self::ACTION_NAME) {
            return;
        }
        // $category = $this->registry->registry('current_category');
        // if (!empty($category) && $category->getLevel() == '4') {
        //     /** @var \Magento\Framework\View\Layout $layout */
        //     $layout = $observer->getLayout();
        //     $layout->getUpdate()->addHandle(self::ACTION_NAME . '_forth_level');
        // }
        // if (!empty($category) && $category->getLevel() == '3') {
        //     /** @var \Magento\Framework\View\Layout $layout */
        //     $layout = $observer->getLayout();
        //     $layout->getUpdate()->addHandle(self::ACTION_NAME . '_third_level');
        // }
    }
}