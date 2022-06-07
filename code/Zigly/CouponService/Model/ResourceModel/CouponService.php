<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_CouponService
 */
declare(strict_types=1);

namespace Zigly\CouponService\Model\ResourceModel;

class CouponService extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('zigly_couponservice_couponservice', 'couponservice_id');
    }
}

