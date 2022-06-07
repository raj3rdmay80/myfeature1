<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomerReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Model\ResourceModel\GroomerReview\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

/**
 * GroomerReview grid collection
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
        $mainTable = 'zigly_groomerreview_groomerreview',
        $resourceModel = \Zigly\GroomerReview\Model\ResourceModel\GroomerReview::class
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
        $this->getSelect()->join(['service'=>'zigly_service_grooming'],"main_table.service_id = service.entity_id",['service' => 'CONCAT("Grooming"," - ",service.center)', 'plan' => 'service.plan_name']);
        $this->getSelect()->join(['groomer'=>'zigly_groomer_groomer'],"main_table.groomer_id = groomer.groomer_id",['groomer_name' => 'groomer.name']);
        $this->getSelect()->join(['order'=>'sales_order'],"service.entity_id = order.booking_id",['order_id' => 'order.increment_id']);
        $this->getSelect()->join(['customer'=>'customer_entity'],"service.customer_id = customer.entity_id",['customer_name' => 'customer.firstname']);
        $this->addFilterToMap('service', new \Zend_Db_Expr('CONCAT("Grooming"," - ",service.center)'));
        $this->addFilterToMap('plan', 'service.plan_name');
        $this->addFilterToMap('groomer_name', 'groomer.name');
        $this->addFilterToMap('order_id', 'order.increment_id');
        $this->addFilterToMap('customer_name', 'customer.firstname');
        return $this;
    }
}