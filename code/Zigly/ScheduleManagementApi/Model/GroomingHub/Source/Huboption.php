<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Zigly\ScheduleManagementApi\Model\GroomingHub\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingHub\CollectionFactory as HuboptionCollectionFactory;

/**
 * Class SpeciesOptions
 */
class Huboption implements OptionSourceInterface
{
    /**
     * @var Zigly\ScheduleManagementApi\\Model\ResourceModel\GroomingHub\CollectionFactory 
     */
    protected $HuboptionCollectionFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param HuboptionCollectionFactory $HuboptionCollectionFactory
     */
    public function __construct(HuboptionCollectionFactory $HuboptionCollectionFactory)
    {
        $this->HuboptionCollectionFactory = $HuboptionCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $collection = $this->HuboptionCollectionFactory->create()
            ->addFieldToFilter('status', 1);
            //print_r($collection->getData()); exit('ss');
        $options = [];
        foreach ($collection as $hub) {
            $options[] = [
                'label' => $hub['hub_name'],
                'value' => $hub['hub_id'],
            ];
        }
        $this->options = $options;

        return $options;

    }

}
