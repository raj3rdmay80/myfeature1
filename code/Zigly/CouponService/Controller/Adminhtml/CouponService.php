<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_CouponService
 */declare(strict_types=1);

namespace Zigly\CouponService\Controller\Adminhtml;

abstract class CouponService extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Zigly_CouponService::top_level';
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Zigly'), __('Zigly'))
            ->addBreadcrumb(__('Coupon'), __('Coupon'));
        return $resultPage;
    }
}

