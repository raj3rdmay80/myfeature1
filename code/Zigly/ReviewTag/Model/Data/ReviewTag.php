<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Model\Data;

use Zigly\ReviewTag\Api\Data\ReviewTagInterface;

class ReviewTag extends \Magento\Framework\Api\AbstractExtensibleObject implements ReviewTagInterface
{

    /**
     * Get reviewtag_id
     * @return string|null
     */
    public function getReviewTagId()
    {
        return $this->_get(self::REVIEWTAG_ID);
    }

    /**
     * Set reviewtag_id
     * @param string $reviewTagId
     * @return \Zigly\ReviewTag\Api\Data\ReviewTagInterface
     */
    public function setReviewTagId($reviewTagId)
    {
        return $this->setData(self::REVIEWTAG_ID, $reviewTagId);
    }

    /**
     * Get location
     * @return string|null
     */
    public function getTagName()
    {
        return $this->_get(self::TAG_NAME);
    }

    /**
     * Set location
     * @param string $location
     * @return \Zigly\ReviewTag\Api\Data\ReviewTagInterface
     */
    public function setTagName($tagName)
    {
        return $this->setData(self::TAG_NAME, $tagName

        );
    }



    /**
     * Get city
     * @return string|null
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    /**
     * Set city
     * @param string $city
     * @return \Zigly\ReviewTag\Api\Data\ReviewTagInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    public function getUpdateAt()
    {
        return $this->_get(self::UPDATE_AT);
    }
    /**
     * Set UpdateTime.
     */
    public function setUpdateAt($updateAt)
    {
        return $this->setData(self::UPDATE_AT, $updateAt);
    }
    /**
     * Get CreatedAt.
     *
     * @return varchar
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set CreatedAt.
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }


}

