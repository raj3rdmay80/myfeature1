<?php
declare(strict_types=1);

namespace Zigly\Mobilehome\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface MobilehomeRepositoryInterface
{

    /**
     * Save mobilehome
     * @param \Zigly\Mobilehome\Api\Data\MobilehomeInterface $mobilehome
     * @return \Zigly\Mobilehome\Api\Data\MobilehomeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Zigly\Mobilehome\Api\Data\MobilehomeInterface $mobilehome
    );

    /**
     * Retrieve mobilehome
     * @param string $mobilehomeId
     * @return \Zigly\Mobilehome\Api\Data\MobilehomeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($mobilehomeId);

    /**
     * Retrieve mobilehome matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\Mobilehome\Api\Data\MobilehomeSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete mobilehome
     * @param \Zigly\Mobilehome\Api\Data\MobilehomeInterface $mobilehome
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Zigly\Mobilehome\Api\Data\MobilehomeInterface $mobilehome
    );

    /**
     * Delete mobilehome by ID
     * @param string $mobilehomeId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($mobilehomeId);
}

