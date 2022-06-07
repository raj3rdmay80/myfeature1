<?php
/**
* Copyright (C) 2021  Zigly
* @package   Zigly_Plan
*/
namespace Zigly\Plan\Model;

use Zigly\Plan\Model\PlanFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\OptionSourceInterface;
use Zigly\Activities\Model\ResourceModel\Activities\CollectionFactory;

class Activities implements OptionSourceInterface
{

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Http $request,
        PlanFactory $planFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->request = $request;
        $this->planFactory = $planFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get cities row type array for option element.
     * @return array
     */
    public function toOptionArray()
    {
        $planDetails = $this->getPlanDetails();
        $plans = $this->getActivitiesById($planDetails->getDefinedActivity());
        $options = [];
        foreach ($plans as $index => $value) {
            $options[] = ['value' => $value['activities_id'], 'label' => $value['activity_name']];
        }
        return $options;
    }

    /**
     * get plan details
     * @return void
     */
    public function getPlanDetails()
    {
        $id = $this->request->getParam('plan_id');
        $plan = $this->planFactory->create();
        if($id){
           $plan = $this->planFactory->create()->load($id);
        }
        return $plan;
    }

    /**
     * get activities by id
     * @return void
     */
    public function getActivitiesById($id = null)
    {
        $id = ($id) ? $id : 0;
        $breed = $this->request->getParam('breed');
        $planId = $this->request->getParam('plan_id');
        $activityCollection = $this->collectionFactory->create();
        $activityCollection->addFieldToFilter(
            ['is_active','activities_id'],
            [
                ['eq' =>1],
                ['in'=>$id]
            ]
        );
        if ($breed){
            $activityCollection->addFieldToFilter('species',['eq' => $breed]);
        } else {
            $plan = $this->planFactory->create()->load($planId);
            $activityCollection->addFieldToFilter('species',['eq' => $plan->getSpecies()]);
        }
        return $activityCollection;
    }

}
