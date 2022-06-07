<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_CouponService
 */
declare(strict_types=1);

namespace Zigly\CouponService\Model\ResourceModel\CouponService;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'couponservice_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Zigly\CouponService\Model\CouponService::class,
            \Zigly\CouponService\Model\ResourceModel\CouponService::class
        );
    }
}

