<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Api;

/**
 * @api
 */
interface TabsRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\CustomTabs\Api\Data\TabsInterface $tabs
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function save(\Amasty\CustomTabs\Api\Data\TabsInterface $tabs);

    /**
     * Get by id
     *
     * @param int $tabId
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($tabId);

    /**
     * Delete
     *
     * @param \Amasty\CustomTabs\Api\Data\TabsInterface $tabs
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\CustomTabs\Api\Data\TabsInterface $tabs);

    /**
     * Delete by id
     *
     * @param int $tabId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($tabId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Duplicate
     *
     * @param \Amasty\CustomTabs\Api\Data\TabsInterface $tab
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function duplicate(\Amasty\CustomTabs\Api\Data\TabsInterface $tab);

    /**
     * @param int $storeId
     * @param int[] $updateTabsIds
     *
     * @return void
     */
    public function deleteOutdatedTabs($storeId, $updateTabsIds);
}
