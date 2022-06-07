<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProfessionalGraphQl
 */
declare(strict_types=1);

namespace Zigly\ProfessionalGraphQl\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface;
use Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterfaceFactory;

class Professional extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'zigly_groomer_groomer';
    protected $professionalDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ProfessionalGraphQlInterfaceFactory $professionalDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zigly\Groomer\Model\ResourceModel\Groomer $resource
     * @param \Zigly\Groomer\Model\ResourceModel\Groomer\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\Context $context,
        \Zigly\Groomer\Model\ResourceModel\Groomer $resource,
        ProfessionalGraphQlInterfaceFactory $professionalDataFactory,
        \Zigly\Groomer\Model\ResourceModel\Groomer\Collection $resourceCollection,
        array $data = []
    ) {
        $this->professionalDataFactory = $professionalDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve groomer model with groomer data
     * @return GroomerInterface
     */
    public function getDataModel()
    {
        $professionalData = $this->getData();
        
        $professionalDataObject = $this->professionalDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $professionalDataObject,
            $professionalData,
            ProfessionalGraphQlInterface::class
        );
        
        return $professionalDataObject;
    }
}

