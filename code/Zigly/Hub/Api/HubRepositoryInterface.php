<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Hub
 */
declare(strict_types=1);

namespace Zigly\Hub\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface HubRepositoryInterface
{

    /**
     * Save Hub
     * @param \Zigly\Hub\Api\Data\HubInterface $hub
     * @return \Zigly\Hub\Api\Data\HubInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Zigly\Hub\Api\Data\HubInterface $hub);

    /**
     * Retrieve Hub
     * @param string $hubId
     * @return \Zigly\Hub\Api\Data\HubInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($hubId);

    /**
     * Retrieve Hub matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\Hub\Api\Data\HubSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Hub
     * @param \Zigly\Hub\Api\Data\HubInterface $hub
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Zigly\Hub\Api\Data\HubInterface $hub);

    /**
     * Delete Hub by ID
     * @param string $hubId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($hubId);
}

