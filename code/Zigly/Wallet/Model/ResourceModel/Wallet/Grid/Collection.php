<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Model\ResourceModel\Wallet\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

/**
 * Wallet grid collection
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'zigly_wallet_wallet',
        $resourceModel = \Zigly\Wallet\Model\ResourceModel\Wallet::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $tableDescription = $this->getConnection()->describeTable($this->getMainTable());
        foreach ($tableDescription as $columnInfo) {
            $this->addFilterToMap($columnInfo['COLUMN_NAME'], 'main_table.' . $columnInfo['COLUMN_NAME']);
        }
        $joinTable = $this->getTable('customer_entity');
        $this->getSelect()->joinLeft($joinTable.' as customer', 'main_table.customer_id = customer.entity_id',['customer_id' => 'customer.email']);
        $this->addFieldToFilter('visibility', ['eq' => 1]);
        $this->addFilterToMap('created_at', 'main_table.created_at');
        $this->addFilterToMap('customer_id', 'customer.email');
        return $this;
    }

/*    protected function _renderFiltersBefore()
     {
        $joinTable = $this->getTable('customer_entity');
        $this->getSelect()->joinLeft($joinTable.' as customer', 'main_table.customer_id = customer.entity_id',['customer_id' => 'customer.email']);
       parent::_renderFiltersBefore();
   }*/
}