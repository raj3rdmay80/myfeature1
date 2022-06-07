<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Api\Data;

interface TabsInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const TAB_ID = 'tab_id';
    const SORT_ORDER = 'sort_order';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const TAB_NAME = 'tab_name';
    const TAB_TITLE = 'tab_title';
    const STATUS = 'status';
    const IS_ACTIVE = 'is_active';
    const CUSTOMER_GROUPS = 'customer_groups';
    const CONTENT = 'content';
    const RELATED_ENABLED = 'related_enabled';
    const UPSELL_ENABLED = 'upsell_enabled';
    const CROSSSELL_ENABLED = 'crosssell_enabled';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const TAB_TYPE = 'type';
    const NAME_IN_LAYOUT = 'name_in_layout';
    const MODULE_NAME = 'module_name';
    /**#@-*/

    const STORE_TABLE_NAME = 'amasty_customtabs_tabs_store';

    /**
     * @return int
     */
    public function getTabId();

    /**
     * @param int $tabId
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setTabId($tabId);

    /**
     * @return int|null
     */
    public function getSortOrder();

    /**
     * @param int|null $sortOrder
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $updatedAt
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setCreatedAt($updatedAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $createdAt
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setUpdatedAt($createdAt);

    /**
     * @return string
     */
    public function getTabName();

    /**
     * @param string $tabName
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setTabName($tabName);

    /**
     * @return string
     */
    public function getTabTitle();

    /**
     * @param string $tabTitle
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setTabTitle($tabTitle);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getIsActive();

    /**
     * @param int $isActive
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setIsActive($isActive);

    /**
     * @return string
     */
    public function getCustomerGroups();

    /**
     * @param string $customerGroups
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setCustomerGroups($customerGroups);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param string $content
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setContent($content);

    /**
     * @return int
     */
    public function getRelatedEnabled();

    /**
     * @param int $relatedEnabled
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setRelatedEnabled($relatedEnabled);

    /**
     * @return int
     */
    public function getUpsellEnabled();

    /**
     * @param int $upsellEnabled
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setUpsellEnabled($upsellEnabled);

    /**
     * @return int
     */
    public function getCrosssellEnabled();

    /**
     * @param int $crosssellEnabled
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setCrosssellEnabled($crosssellEnabled);

    /**
     * @return string
     */
    public function getConditionsSerialized();

    /**
     * @param string $conditionsSerialized
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setConditionsSerialized($conditionsSerialized);

    /**
     * @return int
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getModuleName();

    /**
     * @param string $moduleName
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setModuleName($moduleName);

    /**
     * @return string
     */
    public function getNameInLayout();

    /**
     * @param string $nameInLayout
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function setNameInLayout($nameInLayout);

    /**
     * @param int $storeId
     *
     * @return \Amasty\CustomTabs\Api\Data\TabsInterface
     */
    public function addStore($storeId);

    /**
     * @return string[]
     */
    public function getStores();

    /**
     * @return string[]
     */
    public function getIdentities();
}
