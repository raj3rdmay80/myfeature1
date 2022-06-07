<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Model\Tabs\ResourceModel;

use Amasty\CustomTabs\Api\Data\TabsInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\DB\Helper;

/**
 * Class Grid
 */
class Grid extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    use CollectionTrait;

    /**
     * @var Helper
     */
    protected $dbHelper;

    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        Helper $dbHelper,
        $mainTable = \Amasty\CustomTabs\Model\Tabs\ResourceModel\Tabs::TABLE_NAME,
        $resourceModel = \Amasty\CustomTabs\Model\Tabs\ResourceModel\Tabs::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
        $this->dbHelper = $dbHelper;
    }

    /**
     * Set resource model and determine field mapping
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_map['fields']['stores'] = 'stores_table.store_id';
        $this->_map['fields']['tab_id'] = 'main_table.tab_id';
        parent::_construct();
    }

    /**
     * @inheritdoc
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStores($this->getSelect());
        parent::_renderFiltersBefore();
    }
}
