<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_CouponService
 */
declare(strict_types=1);

namespace Zigly\CouponService\Api\Data;

interface CouponServiceSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get CouponService list.
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Zigly\CouponService\Api\Data\CouponServiceInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

