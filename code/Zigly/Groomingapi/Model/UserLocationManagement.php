<?php
/**
 * Copyright © 2022 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Groomingapi\Model;

use Zigly\Cityscreen\Model\ResourceModel\Cityscreen\CollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class UserLocationManagement implements \Zigly\Groomingapi\Api\UserLocationManagementInterface
{

    
    /**
     * Constructor
     *

     * @param CollectionFactory $cityscreenCollection
     */
    public function __construct(
        CollectionFactory $cityscreenCollection,
        \Magento\Framework\Webapi\Rest\Request $request,
         JsonFactory $jsonResultFactory
    ) {
        $this->cityscreenCollection = $cityscreenCollection;
        $this->request = $request;
        $this->jsonResultFactory = $jsonResultFactory;
 
    }
    /**
     * {@inheritdoc}
     */
    public function getUserLocation($pincode_city)
    {
        $data = $this->request->getParams();
        $pincode_city = $data['pincode_city'];
      
      
        try{
            $output = preg_match( '/^[1-9][0-9]*$/', $pincode_city );
            if($output){
                //pincode
             $city = $this->cityscreenCollection->create()
                 ->addFieldToFilter('pincode', ['eq' => $pincode_city]);
            }else{
             //City Name
                $city = $this->cityscreenCollection->create()
                ->addFieldToFilter('city', ['eq' => $pincode_city]);
            }
            
            $city->getData();
            if (count($city)){
                $responseData['cities'] = $city->getData();
            }else{
                $responseData['message'] = 'Can\'t find the city. please try Searching';
            }
        }catch (\Exception $e) {
            $responseData['message'] = 'Can\'t find the city. please try Searching';
            $responseData['trace'] = $e->getMessage();
        }
        $result = $this->jsonResultFactory->create();
        $result->setData($responseData);
        return $responseData;

    }
}

