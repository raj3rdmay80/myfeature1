<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Model\ResourceModel\Customer\Grid;

use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Customer\Model\ResourceModel\Customer;
use Magento\Customer\Ui\Component\DataProvider\Document;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Psr\Log\LoggerInterface as Logger;


/**
 * Wallet grid collection
 */
class Collection extends \Magento\Customer\Model\ResourceModel\Grid\Collection
{

    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param ResolverInterface $localeResolver
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        Attribute $eavAttribute,
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        ResolverInterface $localeResolver,
        $mainTable = 'customer_grid_flat',
        $resourceModel = Customer::class
    ) {
        $this->eavAttribute = $eavAttribute;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $localeResolver, $mainTable, $resourceModel);
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $attributeId = $this->eavAttribute->getIdByCode('customer', 'wallet_balance');
        $joinTable = $this->getTable('customer_entity_int');
        $this->getSelect()->joinLeft($joinTable.' as customer', '(customer.entity_id = main_table.entity_id AND customer.attribute_id = '.$attributeId.')',['wallet_balance' => 'customer.value']);
        $this->addFilterToMap('wallet_balance_render', 'wallet_balance');
        return $this;
    }
}