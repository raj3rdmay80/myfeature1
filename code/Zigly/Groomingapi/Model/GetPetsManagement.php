<?php
/**
 * Copyright © 2022 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Groomingapi\Model;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class GetPetsManagement implements \Zigly\Groomingapi\Api\GetPetsManagementInterface
{

    protected $_customerSession; 
    public function __construct(

        \Zigly\Managepets\Model\ResourceModel\Managepets\CollectionFactory $collectionFactory,
        \Webkul\MobikulCore\Helper\Data $helper,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Webapi\Rest\Request $request
    ) {

        $this->collectionFactory = $collectionFactory;
        $this->_request = $request;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getGetPets($customerIdtoken)
    {
        try{
            echo "2223444"; exit;
            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $customerId = $this->helper->getCustomerByToken($customerIdtoken);
            
            $customerIdtoken ="";
            if (empty($customerId)) {
                $this->returnArray["otherError"] = "customerNotExist";
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("Customer you are requesting does not exist.")
                );
            }

            $collection = $this->collectionFactory->create()
            ->addFieldtoFilter('customer_id',$customerId)
            ->addFieldtoFilter('delete_status',['eq'=>0]);
            $collection->getSelect()
            ->joinLeft(
                ['species'=>'zigly_species_species'],
                "main_table.type = species.species_id",
                [
                    'speciesname' => 'species.name'
                ]
            );
            $collection->getSelect()
            ->joinLeft(
                ['breed'=>'zigly_species_breed'],
                "main_table.breed = breed.breed_id",
                [
                    'breedname' => 'breed.name'
                ]
            );
  
            if($collection->getData()){
                $collection_data = array();
                foreach($collection->getData() as $key=>$data){
                  $filepath = array("filepath" => $mediaUrl."zigly".$data['filepath']);
                  $collection_data[] = array_merge($data,$filepath);
                }

                return $collection_data; //$collection->getData();
            }else{
                $data = ['status'=> "false", 'message' => "No Pet Availabe in your account"];
                $response = new \Magento\Framework\DataObject();
                $response->setStatus($data['status']);
                $response->setMessage($data['message']);
                return $response;
            }
        
        } catch (\Exception $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }

    }
}

