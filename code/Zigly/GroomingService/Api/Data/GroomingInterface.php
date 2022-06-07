<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Api\Data;

interface GroomingInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const ENTITY_ID = 'entity_id';
    const UPDATED_AT = 'updated_at';
    const BOOKING_STATUS = 'booking_status';
    const ADDRESS_DATA = 'address_data';
    const SCHEDULED_DATE = 'scheduled_date';
    const GRAND_TOTAL = 'grand_total';
    const SCHEDULED_TIME = 'scheduled_time';
    const CUSTOMER_ID = 'customer_id';
    const CREATED_AT = 'created_at';
    const SUBTOTAL = 'subtotal';
    const CENTER = 'center';
    const SHEDULED_DATE = 'sheduled_date';
    const PET_ID = 'pet_id';
    const PAYMENT_MODE = 'payment_mode';
    const COUPON_CODE = 'coupon_code';
    const PLAN_DATA = 'plan_data';
    const IS_CUSTOM = 'is_custom';

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setEntityId($entityId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setCustomerId($customerId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\GroomingService\Api\Data\GroomingExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Zigly\GroomingService\Api\Data\GroomingExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\GroomingService\Api\Data\GroomingExtensionInterface $extensionAttributes
    );

    /**
     * Get plan_data
     * @return string|null
     */
    public function getPlanData();

    /**
     * Set plan_data
     * @param string $planData
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setPlanData($planData);

    /**
     * Get subtotal
     * @return string|null
     */
    public function getSubtotal();

    /**
     * Set subtotal
     * @param string $subtotal
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setSubtotal($subtotal);

    /**
     * Get grand_total
     * @return string|null
     */
    public function getGrandTotal();

    /**
     * Set grand_total
     * @param string $grandTotal
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setGrandTotal($grandTotal);

    /**
     * Get is_custom
     * @return string|null
     */
    public function getIsCustom();

    /**
     * Set is_custom
     * @param string $isCustom
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setIsCustom($isCustom);

    /**
     * Get coupon_code
     * @return string|null
     */
    public function getCouponCode();

    /**
     * Set coupon_code
     * @param string $couponCode
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setCouponCode($couponCode);

    /**
     * Get payment_mode
     * @return string|null
     */
    public function getPaymentMode();

    /**
     * Set payment_mode
     * @param string $paymentMode
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setPaymentMode($paymentMode);

    /**
     * Get address_data
     * @return string|null
     */
    public function getAddressData();

    /**
     * Set address_data
     * @param string $addressData
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setAddressData($addressData);

    /**
     * Get scheduled_date
     * @return string|null
     */
    public function getScheduledDate();

    /**
     * Set scheduled_date
     * @param string $scheduledDate
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setScheduledDate($scheduledDate);

    /**
     * Get scheduled_time
     * @return string|null
     */
    public function getScheduledTime();

    /**
     * Set scheduled_time
     * @param string $scheduledTime
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setScheduledTime($scheduledTime);

    /**
     * Get booking_status
     * @return string|null
     */
    public function getBookingStatus();

    /**
     * Set booking_status
     * @param string $bookingStatus
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setBookingStatus($bookingStatus);

    /**
     * Get sheduled_date
     * @return string|null
     */
    public function getSheduledDate();

    /**
     * Set sheduled_date
     * @param string $sheduledDate
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setSheduledDate($sheduledDate);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get center
     * @return string|null
     */
    public function getCenter();

    /**
     * Set center
     * @param string $center
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setCenter($center);

    /**
     * Get pet_id
     * @return string|null
     */
    public function getPetId();

    /**
     * Set pet_id
     * @param string $petId
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setPetId($petId);
}

