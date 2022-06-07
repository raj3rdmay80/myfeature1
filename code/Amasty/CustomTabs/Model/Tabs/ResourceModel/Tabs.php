<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Model\Tabs\ResourceModel;

use Amasty\CustomTabs\Api\Data\TabsInterface;
use Amasty\CustomTabs\Model\Tabs\Tabs as TabsModel;
use Amasty\CustomTabs\Model\Source\Type;
use Magento\Framework\DB\Helper;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Tabs
 */
class Tabs extends AbstractDb
{
    const TABLE_NAME = 'amasty_customtabs_tabs';

    /**
     * @var Helper
     */
    private $dbHelper;

    public function __construct(
        Helper $dbHelper,
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->dbHelper = $dbHelper;
    }

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, TabsInterface::TAB_ID);
    }

    /**
     * @inheritdoc
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $this->joinStores($select);

        return $select;
    }

    /**
     * @inheritdoc
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->updateStores($object);

        return parent::_afterSave($object);
    }

    /**
     * @param TabsModel $object
     */
    private function updateStores($object)
    {
        $connection = $this->getConnection();
        $tabId = $object->getTabId();

        $table = $this->getTable(TabsInterface::STORE_TABLE_NAME);
        $select = $select = $connection->select()
            ->from($table, 'store_id')
            ->where(TabsInterface::TAB_ID . ' = ?', $tabId);
        $oldData = $connection->fetchCol($select);
        $newData = $object->getStores();

        if (is_array($newData)) {
            $toDelete = array_diff($oldData, $newData);
            $toInsert = array_diff($newData, $oldData);
            $toInsert = array_diff($toInsert, ['']);
        } else {
            $toDelete = $oldData;
            $toInsert = null;
        }

        if (!empty($toDelete)) {
            $deleteSelect = clone $select;
            $deleteSelect->where('store_id IN (?)', $toDelete);
            $query = $connection->deleteFromSelect($deleteSelect, $table);
            $connection->query($query);
        }
        if (!empty($toInsert)) {
            $insertArray = [];
            foreach ($toInsert as $value) {
                $insertArray[] = [TabsInterface::TAB_ID => $tabId, 'store_id' => $value];
            }
            $connection->insertMultiple($table, $insertArray);
        }
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param bool $group
     */
    public function joinStores($select, $group = true)
    {
        $table = $this->getTable(self::TABLE_NAME);
        $select->joinLeft(
            ['stores_table' => $this->getTable(TabsInterface::STORE_TABLE_NAME)],
            $table . '.' . TabsInterface::TAB_ID . ' = stores_table.' . TabsInterface::TAB_ID,
            []
        );
        if ($group) {
            $this->dbHelper->addGroupConcatColumn(
                $select,
                'stores',
                'DISTINCT stores_table.store_id'
            );
        }
    }

    /**
     * @param int $storeId
     * @param array $loadedTabsIds
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteOutdatedTabs($storeId, $loadedTabsIds)
    {
        $connection = $this->getConnection();

        $select = $select = $connection->select()
            ->from($this->getMainTable(), 'tab_id')
            ->where($this->getMainTable() . '.' . TabsInterface::TAB_ID . ' NOT IN (?)', $loadedTabsIds)
            ->where(TabsInterface::TAB_TYPE . ' != ?', Type::CUSTOM);
        $this->joinStores($select);
        $select->where('store_id = ?', $storeId);

        $query = $connection->deleteFromSelect($select, $this->getMainTable());
        $connection->query($query);
    }
}
