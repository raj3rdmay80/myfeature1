<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Model\Tabs;

use Amasty\CustomTabs\Api\Data\TabsInterface;
use Amasty\CustomTabs\Api\TabsRepositoryInterface;
use Amasty\CustomTabs\Model\Tabs\TabsFactory;
use Amasty\CustomTabs\Model\Tabs\ResourceModel\Tabs as TabsResource;
use Amasty\CustomTabs\Model\Tabs\ResourceModel\CollectionFactory;
use Amasty\CustomTabs\Model\Tabs\ResourceModel\Collection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Repository implements TabsRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var TabsFactory
     */
    private $tabsFactory;

    /**
     * @var TabsResource
     */
    private $tabsResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $tabCache;

    /**
     * @var CollectionFactory
     */
    private $tabsCollectionFactory;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        TabsFactory $tabsFactory,
        TabsResource $tabsResource,
        CollectionFactory $tabsCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->tabsFactory = $tabsFactory;
        $this->tabsResource = $tabsResource;
        $this->tabsCollectionFactory = $tabsCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(TabsInterface $tabs)
    {
        try {
            if ($tabs->getTabId()) {
                $tabs = $this->getById($tabs->getTabId())->addData($tabs->getData());
            }
            $this->tabsResource->save($tabs);
            unset($this->tabCache[$tabs->getTabId()]);
        } catch (\Exception $e) {
            if ($tabs->getTabId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save tabs with ID %1. Error: %2',
                        [$tabs->getTabId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new tabs. Error: %1', $e->getMessage()));
        }

        return $tabs;
    }

    /**
     * @inheritdoc
     */
    public function getById($tabId)
    {
        if (!isset($this->tabCache[$tabId])) {
            /** @var \Amasty\CustomTabs\Model\Tabs\Tabs $tabs */
            $tabs = $this->tabsFactory->create();
            $this->tabsResource->load($tabs, $tabId);
            if (!$tabs->getTabId()) {
                throw new NoSuchEntityException(__('Tabs with specified ID "%1" not found.', $tabId));
            }
            $this->tabCache[$tabId] = $tabs;
        }

        return $this->tabCache[$tabId];
    }

    /**
     * @inheritdoc
     */
    public function delete(TabsInterface $tabs)
    {
        try {
            $this->tabsResource->delete($tabs);
            unset($this->tabCache[$tabs->getTabId()]);
        } catch (\Exception $e) {
            if ($tabs->getTabId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove tabs with ID %1. Error: %2',
                        [$tabs->getTabId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove tabs. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($tabId)
    {
        $tabsModel = $this->getById($tabId);
        $this->delete($tabsModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\CustomTabs\Model\Tabs\ResourceModel\Collection $tabsCollection */
        $tabsCollection = $this->tabsCollectionFactory->create();
        
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $tabsCollection);
        }
        
        $searchResults->setTotalCount($tabsCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        
        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $tabsCollection);
        }
        
        $tabsCollection->setCurPage($searchCriteria->getCurrentPage());
        $tabsCollection->setPageSize($searchCriteria->getPageSize());
        
        $tabsData = [];
        /** @var TabsInterface $tabs */
        foreach ($tabsCollection->getItems() as $tabs) {
            $tabsData[] = $this->getById($tabs->getTabId());
        }
        
        $searchResults->setItems($tabsData);

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function duplicate(TabsInterface $tab)
    {
        $tab->setTabId(null);
        $tab->setTabName(__('Copy of ') . $tab->getTabName());
        $tab->setData('store_id', $tab->getData('stores'));
        $tab->setStatus(0);
        $tab->setCreatedAt(null);
        $tab->setUpdatedAt(null);

        $this->save($tab);
        return $tab;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $tabsCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $tabsCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $tabsCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $tabsCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $tabsCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $tabsCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function deleteOutdatedTabs($storeId, $updateTabsIds)
    {
        $this->tabsResource->deleteOutdatedTabs($storeId, $updateTabsIds);
    }
}
