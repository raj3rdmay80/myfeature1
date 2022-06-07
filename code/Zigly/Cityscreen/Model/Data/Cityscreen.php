<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Cityscreen
 */
declare(strict_types=1);

namespace Zigly\Cityscreen\Model\Data;

use Zigly\Cityscreen\Api\Data\CityscreenInterface;

class Cityscreen extends \Magento\Framework\Api\AbstractExtensibleObject implements CityscreenInterface
{

    /**
     * Get cityscreen_id
     * @return string|null
     */
    public function getCityscreenId()
    {
        return $this->_get(self::CITYSCREEN_ID);
    }

    /**
     * Set cityscreen_id
     * @param string $cityscreenId
     * @return \Zigly\Cityscreen\Api\Data\CityscreenInterface
     */
    public function setCityscreenId($cityscreenId)
    {
        return $this->setData(self::CITYSCREEN_ID, $cityscreenId);
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
     * @return \Zigly\Cityscreen\Api\Data\CityscreenInterface
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\Cityscreen\Api\Data\CityscreenExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Zigly\Cityscreen\Api\Data\CityscreenExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\Cityscreen\Api\Data\CityscreenExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get city
     * @return string|null
     */
    public function getCity()
    {
        return $this->_get(self::CITY);
    }

    /**
     * Set city
     * @param string $city
     * @return \Zigly\Cityscreen\Api\Data\CityscreenInterface
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    /**
     * Set is_active
     * @param string $isActive
     * @return \Zigly\Cityscreen\Api\Data\CityscreenInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
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
     * @return \Zigly\Cityscreen\Api\Data\CityscreenInterface
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
     * @return \Zigly\Cityscreen\Api\Data\CityscreenInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}

