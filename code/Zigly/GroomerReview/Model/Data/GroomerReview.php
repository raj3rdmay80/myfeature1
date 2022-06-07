<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomerReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Model\Data;

use Zigly\GroomerReview\Api\Data\GroomerReviewInterface;

class GroomerReview extends \Magento\Framework\Api\AbstractExtensibleObject implements GroomerReviewInterface
{

    /**
     * Get groomerreview_id
     * @return string|null
     */
    public function getGroomerReviewId()
    {
        return $this->_get(self::GROOMERREVIEW_ID);
    }

    /**
     * Set groomerreview_id
     * @param string $groomerReviewId
     * @return \Zigly\GroomerReview\Api\Data\GroomerReviewInterface
     */
    public function setGroomerReviewId($groomerReviewId)
    {
        return $this->setData(self::GROOMERREVIEW_ID, $groomerReviewId);
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
     * @return \Zigly\GroomerReview\Api\Data\GroomerReviewInterface
     */
    public function setTagName($tagName)
    {
        return $this->setData(self::TAG_NAME, $tagName

        );
    }
    /**
     * Get location
     * @return string|null
     */
    public function getGroomerId()
    {
        return $this->_get(self::GROOMER_ID);
    }
    /**
     * Set location
     * @param string $location
     * @return \Zigly\GroomerReview\Api\Data\GroomerReviewInterface
     */
    public function setGroomerId($groomerId)
    {
        return $this->setData(self::GROOMER_ID, $groomerId);
    }

    /**
     * Get location
     * @return string|null
     */
    public function getStarRating()
    {
        return $this->_get(self::STAR_RAING);
    }

    /**
     * Set location
     * @param string $location
     * @return \Zigly\GroomerReview\Api\Data\GroomerReviewInterface
     */
    public function setStarRating($starRating)
    {
        return $this->setData(self::STAR_RAING, $starRating);
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
     * @return \Zigly\GroomerReview\Api\Data\GroomerReviewInterface
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

