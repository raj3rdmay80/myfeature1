<?php
/**
 * Copyright © 2022 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Groomingapi\Model;
use Zigly\Managepets\Model\ManagepetsFactory;
use Zigly\Species\Model\BreedFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class DeletePetManagement implements \Zigly\Groomingapi\Api\DeletePetManagementInterface
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
        ManagepetsFactory $petsFactory
     ) {
        $this->request = $request;
        $this->pets = $petsFactory;
        $this->breed = $breedFactory;
  

    }


    /**
     * {@inheritdoc}
     */
    public function postDeletePet()
    {
        $petdata = $this->request->getBodyParams();
        
        $id = $petdata['pet_id'];
        try{
            $model = $this->pets->create()->load($id);
                    $model->setDeleteStatus(1);
                    $model->save();
                $data = array("status"=>"true", "message"=>"Pet is Update Successfully.");
                $response = new \Magento\Framework\DataObject();
                $response->setStatus($data['status']);
                $response->setMessage($data['message']);
                return $response;
        } catch (\Exception $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
      
    }
}

