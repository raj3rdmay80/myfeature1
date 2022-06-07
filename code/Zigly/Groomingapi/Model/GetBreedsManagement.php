<?php
/**
 * Copyright © 2022 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Groomingapi\Model;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\Species\Model\ResourceModel\Breed\CollectionFactory as BreedCollectionFactory;

class GetBreedsManagement implements \Zigly\Groomingapi\Api\GetBreedsManagementInterface
{


    public function __construct(

        \Magento\Framework\Webapi\Rest\Request $request,
        BreedCollectionFactory $breedCollectionFactory
    ) {

        $this->_request = $request;
        $this->breedCollectionFactory = $breedCollectionFactory;
    }

    /**
     * {@inheritdoc}
    */
    public function getBreeds($search)
    {
        $data = $this->_request->getParams();
        $search= $data['search'];

      try{
        $collection = $this->breedCollectionFactory->create();
        //->addFieldToFilter('name', ['eq' => $search])
        //->addFieldToSelect('*');
        $collection->addFieldToFilter(
            array('name'),
            array(
                array('like' => '%'.$search.'%')
            )             
        );
        return $collection->getData();
       }catch (\Exception $e) {
        throw new NoSuchEntityException(__($e->getMessage()), $e);
      }

    }
}

