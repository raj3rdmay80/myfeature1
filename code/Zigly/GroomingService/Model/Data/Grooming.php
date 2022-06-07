<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Model\Data;

use Zigly\GroomingService\Api\Data\GroomingInterface;

class Grooming extends \Magento\Framework\Api\AbstractExtensibleObject implements GroomingInterface
{

    /**
     * Get grooming_id
     * @return string|null
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * Set grooming_id
     * @param string $entityId
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\GroomingService\Api\Data\GroomingExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Zigly\GroomingService\Api\Data\GroomingExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\GroomingService\Api\Data\GroomingExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get plan_data
     * @return string|null
     */
    public function getPlanData()
    {
        return $this->_get(self::PLAN_DATA);
    }

    /**
     * Set plan_data
     * @param string $planData
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setPlanData($planData)
    {
        return $this->setData(self::PLAN_DATA, $planData);
    }

    /**
     * Get subtotal
     * @return string|null
     */
    public function getSubtotal()
    {
        return $this->_get(self::SUBTOTAL);
    }

    /**
     * Set subtotal
     * @param string $subtotal
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setSubtotal($subtotal)
    {
        return $this->setData(self::SUBTOTAL, $subtotal);
    }

    /**
     * Get grand_total
     * @return string|null
     */
    public function getGrandTotal()
    {
        return $this->_get(self::GRAND_TOTAL);
    }

    /**
     * Set grand_total
     * @param string $grandTotal
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setGrandTotal($grandTotal)
    {
        return $this->setData(self::GRAND_TOTAL, $grandTotal);
    }

    /**
     * Get is_custom
     * @return string|null
     */
    public function getIsCustom()
    {
        return $this->_get(self::IS_CUSTOM);
    }

    /**
     * Set is_custom
     * @param string $isCustom
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setIsCustom($isCustom)
    {
        return $this->setData(self::IS_CUSTOM, $isCustom);
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
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setCouponCode($couponCode)
    {
        return $this->setData(self::COUPON_CODE, $couponCode);
    }

    /**
     * Get payment_mode
     * @return string|null
     */
    public function getPaymentMode()
    {
        return $this->_get(self::PAYMENT_MODE);
    }

    /**
     * Set payment_mode
     * @param string $paymentMode
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setPaymentMode($paymentMode)
    {
        return $this->setData(self::PAYMENT_MODE, $paymentMode);
    }

    /**
     * Get address_data
     * @return string|null
     */
    public function getAddressData()
    {
        return $this->_get(self::ADDRESS_DATA);
    }

    /**
     * Set address_data
     * @param string $addressData
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setAddressData($addressData)
    {
        return $this->setData(self::ADDRESS_DATA, $addressData);
    }

    /**
     * Get scheduled_date
     * @return string|null
     */
    public function getScheduledDate()
    {
        return $this->_get(self::SCHEDULED_DATE);
    }

    /**
     * Set scheduled_date
     * @param string $scheduledDate
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setScheduledDate($scheduledDate)
    {
        return $this->setData(self::SCHEDULED_DATE, $scheduledDate);
    }

    /**
     * Get scheduled_time
     * @return string|null
     */
    public function getScheduledTime()
    {
        return $this->_get(self::SCHEDULED_TIME);
    }

    /**
     * Set scheduled_time
     * @param string $scheduledTime
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setScheduledTime($scheduledTime)
    {
        return $this->setData(self::SCHEDULED_TIME, $scheduledTime);
    }

    /**
     * Get booking_status
     * @return string|null
     */
    public function getBookingStatus()
    {
        return $this->_get(self::BOOKING_STATUS);
    }

    /**
     * Set booking_status
     * @param string $bookingStatus
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setBookingStatus($bookingStatus)
    {
        return $this->setData(self::BOOKING_STATUS, $bookingStatus);
    }

    /**
     * Get sheduled_date
     * @return string|null
     */
    public function getSheduledDate()
    {
        return $this->_get(self::SHEDULED_DATE);
    }

    /**
     * Set sheduled_date
     * @param string $sheduledDate
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setSheduledDate($sheduledDate)
    {
        return $this->setData(self::SHEDULED_DATE, $sheduledDate);
    }

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
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
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setCenter($center)
    {
        return $this->setData(self::CENTER, $center);
    }

    /**
     * Get pet_id
     * @return string|null
     */
    public function getPetId()
    {
        return $this->_get(self::PET_ID);
    }

    /**
     * Set pet_id
     * @param string $petId
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     */
    public function setPetId($petId)
    {
        return $this->setData(self::PET_ID, $petId);
    }
}

