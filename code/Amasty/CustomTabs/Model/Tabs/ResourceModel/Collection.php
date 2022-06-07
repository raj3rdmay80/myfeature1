<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Model\Tabs\ResourceModel;

use Amasty\CustomTabs\Model\Source\Status;
use Magento\Framework\DB\Helper;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Amasty\CustomTabs\Api\Data\TabsInterface;

class Collection extends AbstractCollection
{
    use CollectionTrait;

    /**
     * @var Helper
     */
    protected $dbHelper;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Helper $dbHelper,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->dbHelper = $dbHelper;
        $this->sessionFactory = $sessionFactory;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \Amasty\CustomTabs\Model\Tabs\Tabs::class,
            \Amasty\CustomTabs\Model\Tabs\ResourceModel\Tabs::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
        $this->_map['fields']['tab_id'] = 'main_table.tab_id';
    }

    /**
     * Try to get mapped field name for filter to collection
     *
     * @param   string $field
     * @return  string
     */
    protected function _getMappedField($field)
    {
        $mapper = $this->_getMapper();

        //fix fatal with zend expression
        if (is_string($field) && isset($mapper['fields'][$field])) {
            $mappedField = $mapper['fields'][$field];
        } else {
            $mappedField = $field;
        }

        return $mappedField;
    }

    /**
     * @return array
     */
    public function getExistingTabs()
    {
        $this->getSelect()->reset(Select::COLUMNS)
            ->columns([TabsInterface::NAME_IN_LAYOUT]);

        $tabs = $this->getConnection()->fetchCol($this->getSelect());

        return $tabs;
    }

    /**
     * @param array $types
     * @param array $tabIds
     *
     * @return $this
     */
    public function getCustomTabByParams($types, $tabIds)
    {
        $this->addFieldToFilter(TabsInterface::STATUS, Status::ENABLED)
            ->addFieldToFilter(TabsInterface::TAB_TYPE, ['in' => $types])
            ->addFieldToFilter(TabsInterface::CONTENT, ['neq' => '']);

        $tabIds[] = 0; //prevent fatal on empty array
        $this->getSelect()
            ->where("CONCAT(',',customer_groups,',') like '%,?,%'", $this->getCurrentCustomerGroupId())
            ->where(sprintf(
                '%s IS NULL OR %s IN(%s)',
                TabsInterface::CONDITIONS_SERIALIZED,
                TabsInterface::TAB_ID,
                implode(',', $tabIds)
            ));
        return $this;
    }

    /**
     * @return int
     */
    protected function getCurrentCustomerGroupId()
    {
        return (int)$this->getCustomerSession()->getCustomerGroupId() ? : 0;
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    private function getCustomerSession()
    {
        return $this->sessionFactory->create();
    }
}
