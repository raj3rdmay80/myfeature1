<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsultingApi
 */
declare(strict_types=1);

namespace Zigly\VetConsultingApi\Api;

interface VetConsultingRepositoryInterface
{
    /**
     * listing vet Consult.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\VetConsultingApi\Api\Data\VetConsultingSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function vetConsultingListing(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * listing view vet consult.
     * @return \Zigly\VetConsultingApi\Api\Data\VetConsultingListingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function vetConsultingById();

}