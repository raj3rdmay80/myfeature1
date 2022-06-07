<?php
/**
 * Copyright © 2022 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Groomingapi\Model;

use Zigly\Managepets\Model\ManagepetsFactory;
use Zigly\Species\Model\BreedFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;

class AddPetManagement implements \Zigly\Groomingapi\Api\AddPetManagementInterface
{

    /**
      * @var \Magento\Framework\Webapi\Rest\Request
    */
    protected $request;
    /**
      * @param \Magento\Framework\Webapi\Rest\Request $request
    */
    public function __construct(
        \Magento\Framework\Webapi\Rest\Request $request,
        BreedFactory $breedFactory,
        ManagepetsFactory $petsFactory,
        \Magento\Framework\Filesystem $fileSystem,
        \Webkul\MobikulCore\Helper\Data $helper,
        \Zigly\Groomingapi\Helper\Data $data,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Image\AdapterFactory $adapterFactory
     ) {
        $this->request = $request;
        $this->pets = $petsFactory;
        $this->breed = $breedFactory;
        $this->data = $data;
        $this->helper = $helper;
        $this->filesystem = $fileSystem;
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;

    }

    /**
     * {@inheritdoc}
     */
    public function postAddPet()
    {
    
        $petdata = $this->request->getParams();
        $filedata = $this->request->getFiles();

        
        try{
            //Set file path with name for save into database
            $imagePath = $this->data->ImageuploadApi($filedata);
            $customerIdtoken = $petdata['customer_id'];

            if(!empty($customerIdtoken)){
                $customerId = $this->helper->getCustomerByToken($customerIdtoken);
                $customerIdtoken ="";
                if (empty($customerId)) {
                    $this->returnArray["otherError"] = "customerNotExist";
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __("Customer you are requesting does not exist.")
                    );
                }
            }
            //if($imagePath != false ){
                 $petdata['filepath'] = $imagePath;
                 $petdata['customer_id'] = $customerId ;
                 $model = $this->pets->create();

            if($model->setData($petdata)->save()){
                 $data = array("status"=>"true", "message"=>"Pet is saved successfully.");
            }else{
                 $data = array("status"=>"false", "message"=>"Pet not Saved.");
            }

            $response = new \Magento\Framework\DataObject();
            $response->setStatus($data['status']);
            $response->setMessage($data['message']);
            return $response;
        } catch (\Exception $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }
}

