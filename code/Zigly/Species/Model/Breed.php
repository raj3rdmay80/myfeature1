<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Species\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zigly\Species\Api\Data\BreedInterface;
use Zigly\Species\Api\Data\BreedInterfaceFactory;

class Breed extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'zigly_species_breed';
    protected $dataObjectHelper;

    protected $breedDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param BreedInterfaceFactory $breedDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zigly\Species\Model\ResourceModel\Breed $resource
     * @param \Zigly\Species\Model\ResourceModel\Breed\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        BreedInterfaceFactory $breedDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Zigly\Species\Model\ResourceModel\Breed $resource,
        \Zigly\Species\Model\ResourceModel\Breed\Collection $resourceCollection,
        array $data = []
    ) {
        $this->breedDataFactory = $breedDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve breed model with breed data
     * @return BreedInterface
     */
    public function getDataModel()
    {
        $breedData = $this->getData();
        
        $breedDataObject = $this->breedDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $breedDataObject,
            $breedData,
            BreedInterface::class
        );
        
        return $breedDataObject;
    }
}

