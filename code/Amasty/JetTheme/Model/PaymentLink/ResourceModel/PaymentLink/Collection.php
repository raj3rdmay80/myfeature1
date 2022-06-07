<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\PaymentLink\ResourceModel\PaymentLink;

use Amasty\JetTheme\Api\Data\PaymentLinkInterface;
use Amasty\JetTheme\Model\PaymentLink\ResourceModel\PaymentLink as PaymentLinkResource;
use Amasty\JetTheme\Model\PaymentLink\PaymentLink;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Helper;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @var Helper
     */
    private $dbHelper;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Helper $dbHelper,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->dbHelper = $dbHelper;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            PaymentLink::class,
            PaymentLinkResource::class
        );
    }

    /**
     * @return Collection
     */
    protected function _beforeLoad()
    {
        $this->joinStores();

        return parent::_beforeLoad();
    }

    /**
     * @return void
     */
    public function joinStores(): void
    {
        $select = $this->getSelect();
        $select->joinLeft(
            ['stores' => $this->getTable(PaymentLinkInterface::STORE_TABLE_NAME)],
            'main_table.'
            . PaymentLinkInterface::ENTITY_ID
            . ' = stores.'
            . PaymentLinkInterface::STORE_PAYMENT_ID_FIELD,
            []
        );

        $select->group('main_table.entity_id');
        $this->dbHelper->addGroupConcatColumn(
            $select,
            'stores',
            'stores.store_id'
        );
    }
}
