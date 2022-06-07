<?php
/**
 * Copyright © 2022 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Groomingapi\Model;

use Zigly\Managepets\Model\ManagepetsFactory;
use Zigly\Species\Model\BreedFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class EditPetsManagement implements \Zigly\Groomingapi\Api\EditPetManagementInterface
{

    /**
      * @var \Magento\Framework\Webapi\Rest\Request
    */
    protected $request;
    /**
      * @param \Magento\Framework\Webapi\Rest\Request $request
    */
    /**
     * @var JsonFactory
    */



    /**
       * @param Context     $context
       * @param JsonFactory $resultJsonFactory
    */
    public function __construct(
         \Magento\Framework\Webapi\Rest\Request $getRequest,
         BreedFactory $breedFactory,
         ManagepetsFactory $petsFactory,
         \Magento\Framework\App\Request\Http $request,
         \Zigly\Groomingapi\Helper\Data $data
     ) {
        $this->request = $request;
        $this->getRequest = $getRequest;
        $this->pets = $petsFactory;
        $this->data = $data;
        $this->breed = $breedFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getEditPets()
    {
       
        $petdata = $this->request->getParams();
        print_r($petdata);
        $filedata = $this->request->getFiles();
        $post = $this->getRequest->getPost();
        print_r($post); exit('ssweqeq');
        
        if(!empty($petdata)){
            $model = $this->pets->create()->load($petdata['petdata']['entity_id']);
            print_r($model->getData()); exit('sss');
            $id = $model->getId();
            $customerId = $model->getCustomerId();
            if(($id == $petdata['petdata']['entity_id']) && ($customerId == $petdata['petdata']['customer_id'])){
              try{
                $imagePath = $this->data->ImageuploadApi($filedata);
    
                $petdata['filepath'] = $imagePath;
                $model->setData($petdata['petdata'])->Save();
                if($model->setData($petdata['petdata'])->Save()){
                  $data = array("status"=>"true", "message"=>"Pet is Update Successfully.");
                }else{

                   $data = array("status"=>"false", "message"=>"Pet not updated."); 
                }
                
                $response = new \Magento\Framework\DataObject();
                $response->setStatus($data['status']);
                $response->setMessage($data['message']);
                return $response;
               
                
              }catch (\Exception $e) {
                throw new NoSuchEntityException(__($e->getMessage()), $e);
              }
            }
        }else{
            $data = array("status"=>"false", "message"=>"There is some error.");
            $response = new \Magento\Framework\DataObject();
            $response->setStatus($data['status']);
            $response->setMessage($data['message']);
            return $response;
         
        }
    }
}
