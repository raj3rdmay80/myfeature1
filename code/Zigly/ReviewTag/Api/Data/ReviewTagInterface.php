<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Api\Data;

interface ReviewTagInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{


    const REVIEWTAG_ID = 'reviewtag_id';
    const TAG_NAME = 'tag_name';
    const IS_ACTIVE = 'is_active';
    const CREATED_AT = 'created_at';
    const UPDATE_AT = 'updated_at';

    /**
     * Get reviewTag_id
     */
    public function getReviewTagId();

    /**
     * Set reviewTag_id
     */
    public function setReviewTagId($reviewTagId);

    /**
     * Get tag_name
     */
    public function getTagName();

    /**
     * Set tag_name
     */
    public function setTagName($tagName);

    /**
     * Get IsActive.
     *
     * @return varchar
     */
    public function getIsActive();

    /**
     * Set StartingPrice.
     */
    public function setIsActive($isActive);

    /**
     * Get UpdateTime.
     *
     * @return varchar
     */
    public function getUpdateAt();

    /**
     * Set UpdateTime.
     */
    public function setUpdateAt($updateAt);

    /**
     * Get CreatedAt.
     *
     * @return varchar
     */
    public function getCreatedAt();

    /**
     * Set CreatedAt.
     */
    public function setCreatedAt($createdAt);
}
