<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Species\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zigly\Species\Api\Data\SpeciesInterface;
use Zigly\Species\Api\Data\SpeciesInterfaceFactory;

class Species extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'zigly_species_species';
    protected $speciesDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param SpeciesInterfaceFactory $speciesDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zigly\Species\Model\ResourceModel\Species $resource
     * @param \Zigly\Species\Model\ResourceModel\Species\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        SpeciesInterfaceFactory $speciesDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Zigly\Species\Model\ResourceModel\Species $resource,
        \Zigly\Species\Model\ResourceModel\Species\Collection $resourceCollection,
        array $data = []
    ) {
        $this->speciesDataFactory = $speciesDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve species model with species data
     * @return SpeciesInterface
     */
    public function getDataModel()
    {
        $speciesData = $this->getData();
        
        $speciesDataObject = $this->speciesDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $speciesDataObject,
            $speciesData,
            SpeciesInterface::class
        );
        
        return $speciesDataObject;
    }
}

