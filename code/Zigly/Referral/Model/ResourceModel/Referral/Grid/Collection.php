<?php
namespace Zigly\Referral\Model\ResourceModel\Referral\Grid;
 
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Search\AggregationInterface;
use Zigly\Referral\Model\ResourceModel\Referral\Collection as ReferralCollection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
 
class Collection extends ReferralCollection implements SearchResultInterface
{
    
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
		\Magento\Framework\DB\Adapter\AdapterInterface  $connection = null,
        AbstractDb $resource = null,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\RequestInterface $requestInterface
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
        $this->request = $request;
        $this->requestInterface = $requestInterface;
    }

    protected function _beforeLoad()
    {
        parent::_beforeLoad();
		
        $params = $this->requestInterface->getPostValue();
        if($this->requestInterface->isPost())
        {
			$customerId = isset($params['customer_id'])?$params['customer_id']:0;
            $this->addFieldToFilter('referred_customer_id',['eq'=>$customerId]);
            $this->addFieldToFilter('order_id',['notnull'=>true]);
		}
        return $this;
    }
 
    public function getAggregations()
    {
        return $this->aggregations;
    }
 
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }
 
    public function getSearchCriteria()
    {
        return null;
    }
 
    public function setSearchCriteria(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null
    ) {
        return $this;
    }
 
    public function getTotalCount()
    {
        return $this->getSize();
    }
 
    public function setTotalCount($totalCount)
    {
        return $this;
    }
 
    public function setItems(array $items = null)
    {
        return $this;
    }
}