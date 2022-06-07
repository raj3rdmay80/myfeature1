<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_CouponService
 */
declare(strict_types=1);

namespace Zigly\CouponService\Api\Data;

interface CouponServiceInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const END_DATE = 'end_date';
    const START_DATE = 'start_date';
    const TYPE = 'type';
    const AMOUNT = 'amount';
    const COUPON_CODE = 'coupon_code';
    const NAME = 'name';
    const CENTER = 'center';
    const COUPONSERVICE_ID = 'couponservice_id';

    /**
     * Get couponservice_id
     * @return string|null
     */
    public function getCouponserviceId();

    /**
     * Set couponservice_id
     * @param string $couponserviceId
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setCouponserviceId($couponserviceId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setName($name);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\CouponService\Api\Data\CouponServiceExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Zigly\CouponService\Api\Data\CouponServiceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\CouponService\Api\Data\CouponServiceExtensionInterface $extensionAttributes
    );

    /**
     * Get start_date
     * @return string|null
     */
    public function getStartDate();

    /**
     * Set start_date
     * @param string $startDate
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setStartDate($startDate);

    /**
     * Get end_date
     * @return string|null
     */
    public function getEndDate();

    /**
     * Set end_date
     * @param string $endDate
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setEndDate($endDate);

    /**
     * Get coupon_code
     * @return string|null
     */
    public function getCouponCode();

    /**
     * Set coupon_code
     * @param string $couponCode
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setCouponCode($couponCode);

    /**
     * Get amount
     * @return string|null
     */
    public function getAmount();

    /**
     * Set amount
     * @param string $amount
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setAmount($amount);

    /**
     * Get center
     * @return string|null
     */
    public function getCenter();

    /**
     * Set center
     * @param string $center
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setCenter($center);

    /**
     * Get type
     * @return string|null
     */
    public function getType();

    /**
     * Set type
     * @param string $type
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     */
    public function setType($type);
}

