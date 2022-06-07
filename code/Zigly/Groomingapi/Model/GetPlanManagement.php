<?php
/**
 * Copyright © 2022 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Groomingapi\Model;

use Zigly\Plan\Model\ResourceModel\Plan\CollectionFactory;
use Zigly\Cityscreen\Model\ResourceModel\Cityscreen\CollectionFactory as CityCollection;
use Zigly\Activities\Model\ResourceModel\Activities as ResourceActivities;
use Zigly\Activities\Model\ActivitiesFactory;
use Zigly\Activities\Model\ResourceModel\Activities\CollectionFactory as ActivitiesCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class GetPlanManagement implements \Zigly\Groomingapi\Api\GetPlanManagementInterface
{
    /**
      * @var \Magento\Framework\Webapi\Rest\Request
    */
    protected $request;
    /**
      * @param \Magento\Framework\Webapi\Rest\Request $request
      * @param CollectionFactory $planCollection
    */
    public function __construct(
         \Magento\Framework\Webapi\Rest\Request $request,
         CityCollection $cities,
         ActivitiesCollectionFactory $activitiesCollectionFactory,
         ActivitiesFactory $activitiesFactory,
         CollectionFactory $planCollection
     ) {
        $this->request = $request;
        $this->cities = $cities;
        $this->activitiesCollectionFactory = $activitiesCollectionFactory;
        $this->activities = $activitiesFactory;
        $this->plans = $planCollection;

    }

    /**
     * {@inheritdoc}
     */
    public function getGetPlan($type, $cities, $species_type)
    {
        $city = $this->request->getParam('cities');
        $type = $this->request->getParam('type');
        $species_type = $this->request->getParam('species_type');
        $planlist = $this->request->getBodyParams();


        
        try{
            if (!empty($city) && (strcasecmp($city, 'New delhi') == 0)) {
                $city = "Delhi";
            }
            $cities = $this->cities->create()->addFieldToSelect('cityscreen_id')->addFieldToFilter('city', ['eq' => $city]);
            $citiesArray = [];
            foreach ($cities as $city) {
                $citiesArray[] = ['finset' => $city->getCityscreenId()];
            }
            
            $plan = $this->getPlans($citiesArray,$type,$species_type);


            if(!empty($plan->getData())){
               $plandata = $plan->getData();
               $collection_plandata = array();
               foreach($plandata as $data){
                    $activity = $data['activity'];
                    $activity_id = explode(',', $activity);
                    $collection_activity = array(); 
                    foreach ($activity_id as $key => $activity_data) {
                        $collection =  $this->activities->create()->load($activity_data);
                        $collection_activity[] = $collection->getData();
                    }

                    $collection_data = array("activity" => $collection_activity); 
                    $collection_plandata[] = array_merge($data,$collection_data);

               }
               echo "<pre>";
               print_r($collection_plandata);
               exit;
               return $collection_plandata;
            }else{
                $data = ['status'=> "false", 'message' => "No Plan are not  Availabe In this loaction"];

                $response = new \Magento\Framework\DataObject();

                $response->setStatus($data['status']);
                $response->setMessage($data['message']);
                return $response;
            }
            
        }catch (\Exception $e) {
            throw new NoSuchEntityException(__($e->getMessage()));
        }
       
    }

    public function getPlans($citiesArray, $type, $species_type)
    {
        $planCollection = '';
        $plantype = $type .",". 3;
        $planCollection = $this->plans->create()
            ->addFieldToFilter('species', $species_type)
            ->addFieldToFilter('plan_type', ['in' => [$plantype]])
            ->addFieldToFilter(
                    ['applicable_cities'],
                    [$citiesArray]
                )
            ->addFieldToFilter('status', 1)
            ->setOrder('sort_order','ASC');
            //echo $planCollection->getSelect(); 
        //print_r($planCollection->getData()); exit('ss');
        return $planCollection;
    }
}

