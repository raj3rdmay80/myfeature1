<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Model\Tabs\DataProvider;

use Amasty\CustomTabs\Model\Tabs\ResourceModel\GridFactory as GridCollectionFactory;
use Amasty\CustomTabs\Model\Source\CustomerGroup;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as CustomerGroupCollectionFactory;
use Magento\Framework\Api\Filter;

/**
 * Class Listing
 */
class Listing extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Amasty\CustomTabs\Model\Tabs\ResourceModel\Grid
     */
    protected $collection;

    /**
     * @var array
     */
    private $customerGroups;

    public function __construct(
        CustomerGroupCollectionFactory $customerGroupCollectionFactory,
        GridCollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->customerGroups = $customerGroupCollectionFactory->create()->getAllIds();
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @param Filter $filter
     * @return mixed|void
     */
    public function addFilter(Filter $filter)
    {
        if ($filter->getField() === 'customer_groups') {
            $select = $this->getCollection()->getSelect();

            $values = $filter->getValue();
            if (!is_array($values)) {
                $values = [$values];
            }

            $query = '';
            foreach ($values as $key => $value) {
                $query .= sprintf(" OR CONCAT(',', customer_groups, ',') LIKE '%s'", '%,' . (int)$value . ',%');
            }

            $query = trim($query, ' OR');
            $select->where('(' . $query . ')');
        } else {
            parent::addFilter($filter);
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        if ($data['totalRecords'] > 0) {
            foreach ($data['items'] as &$item) {
                $item['stores'] = explode(',', $item['stores']);
                $customerGroups = explode(',', $item['customer_groups']);
                $item['customer_groups'] = count($customerGroups) == count($this->customerGroups)
                    ? [CustomerGroup::ALL]
                    : $customerGroups;
            }
        }

        return $data;
    }
}
