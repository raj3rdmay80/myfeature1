<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Api\Data;

interface CommentInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const COMMENT_ID = 'comment_id';
    const CREATED_AT = 'created_at';
    const COMMENT = 'comment';
    const CREATED_BY = 'created_by';
    const SERVICE_ID = 'service_id';

    /**
     * Get comment_id
     * @return string|null
     */
    public function getCommentId();

    /**
     * Set comment_id
     * @param string $commentId
     * @return \Zigly\GroomingService\Api\Data\CommentInterface
     */
    public function setCommentId($commentId);

    /**
     * Get comment
     * @return string|null
     */
    public function getComment();

    /**
     * Set comment
     * @param string $comment
     * @return \Zigly\GroomingService\Api\Data\CommentInterface
     */
    public function setComment($comment);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\GroomingService\Api\Data\CommentExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Zigly\GroomingService\Api\Data\CommentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\GroomingService\Api\Data\CommentExtensionInterface $extensionAttributes
    );

    /**
     * Get service_id
     * @return string|null
     */
    public function getServiceId();

    /**
     * Set service_id
     * @param string $serviceId
     * @return \Zigly\GroomingService\Api\Data\CommentInterface
     */
    public function setServiceId($serviceId);

    /**
     * Get created_by
     * @return string|null
     */
    public function getCreatedBy();

    /**
     * Set created_by
     * @param string $createdBy
     * @return \Zigly\GroomingService\Api\Data\CommentInterface
     */
    public function setCreatedBy($createdBy);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Zigly\GroomingService\Api\Data\CommentInterface
     */
    public function setCreatedAt($createdAt);
}
