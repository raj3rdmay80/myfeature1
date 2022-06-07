<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_CouponService
 */
declare(strict_types=1);

namespace Zigly\CouponService\Model\Data;

use Zigly\CouponService\Api\Data\CouponServiceInterface;

class CouponService extends \Magento\Framework\Api\AbstractExtensibleObject implements CouponServiceInterface
{

    /**
     * Get couponservice_id
     * @return string|null
     */
    public function getCouponserviceId()
    {
        return $this->_get(self::COUPONSERVICE_ID);
    }

    /**
     * Set couponservice_id
     * @param string $couponserviceId
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setCouponserviceId($couponserviceId)
    {
        return $this->setData(self::COUPONSERVICE_ID, $couponserviceId);
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\CouponService\Api\Data\CouponServiceExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Zigly\CouponService\Api\Data\CouponServiceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\CouponService\Api\Data\CouponServiceExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get start_date
     * @return string|null
     */
    public function getStartDate()
    {
        return $this->_get(self::START_DATE);
    }

    /**
     * Set start_date
     * @param string $startDate
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setStartDate($startDate)
    {
        return $this->setData(self::START_DATE, $startDate);
    }

    /**
     * Get end_date
     * @return string|null
     */
    public function getEndDate()
    {
        return $this->_get(self::END_DATE);
    }

    /**
     * Set end_date
     * @param string $endDate
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setEndDate($endDate)
    {
        return $this->setData(self::END_DATE, $endDate);
    }

    /**
     * Get coupon_code
     * @return string|null
     */
    public function getCouponCode()
    {
        return $this->_get(self::COUPON_CODE);
    }

    /**
     * Set coupon_code
     * @param string $couponCode
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setCouponCode($couponCode)
    {
        return $this->setData(self::COUPON_CODE, $couponCode);
    }

    /**
     * Get amount
     * @return string|null
     */
    public function getAmount()
    {
        return $this->_get(self::AMOUNT);
    }

    /**
     * Set amount
     * @param string $amount
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * Get center
     * @return string|null
     */
    public function getCenter()
    {
        return $this->_get(self::CENTER);
    }

    /**
     * Set center
     * @param string $center
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setCenter($center)
    {
        return $this->setData(self::CENTER, $center);
    }

    /**
     * Get type
     * @return string|null
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * Set type
     * @param string $type
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }
}

