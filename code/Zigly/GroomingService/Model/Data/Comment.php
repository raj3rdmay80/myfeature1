<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Model\Data;

use Zigly\GroomingService\Api\Data\CommentInterface;

class Comment extends \Magento\Framework\Api\AbstractExtensibleObject implements CommentInterface
{

    /**
     * Get comment_id
     * @return string|null
     */
    public function getCommentId()
    {
        return $this->_get(self::COMMENT_ID);
    }

    /**
     * Set comment_id
     * @param string $commentId
     * @return \Zigly\GroomingService\Api\Data\CommentInterface
     */
    public function setCommentId($commentId)
    {
        return $this->setData(self::COMMENT_ID, $commentId);
    }

    /**
     * Get comment
     * @return string|null
     */
    public function getComment()
    {
        return $this->_get(self::COMMENT);
    }

    /**
     * Set comment
     * @param string $comment
     * @return \Zigly\GroomingService\Api\Data\CommentInterface
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\GroomingService\Api\Data\CommentExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Zigly\GroomingService\Api\Data\CommentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\GroomingService\Api\Data\CommentExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get service_id
     * @return string|null
     */
    public function getServiceId()
    {
        return $this->_get(self::SERVICE_ID);
    }

    /**
     * Set service_id
     * @param string $serviceId
     * @return \Zigly\GroomingService\Api\Data\CommentInterface
     */
    public function setServiceId($serviceId)
    {
        return $this->setData(self::SERVICE_ID, $serviceId);
    }

    /**
     * Get created_by
     * @return string|null
     */
    public function getCreatedBy()
    {
        return $this->_get(self::CREATED_BY);
    }

    /**
     * Set created_by
     * @param string $createdBy
     * @return \Zigly\GroomingService\Api\Data\CommentInterface
     */
    public function setCreatedBy($createdBy)
    {
        return $this->setData(self::CREATED_BY, $createdBy);
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
     * @return \Zigly\GroomingService\Api\Data\CommentInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
