<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Groomer
 */
declare(strict_types=1);

namespace Zigly\Groomer\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zigly\Groomer\Api\Data\GroomerInterface;
use Zigly\Groomer\Api\Data\GroomerInterfaceFactory;

class Groomer extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'zigly_groomer_groomer';
    protected $groomerDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param GroomerInterfaceFactory $groomerDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zigly\Groomer\Model\ResourceModel\Groomer $resource
     * @param \Zigly\Groomer\Model\ResourceModel\Groomer\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        GroomerInterfaceFactory $groomerDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Zigly\Groomer\Model\ResourceModel\Groomer $resource,
        \Zigly\Groomer\Model\ResourceModel\Groomer\Collection $resourceCollection,
        array $data = []
    ) {
        $this->groomerDataFactory = $groomerDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve groomer model with groomer data
     * @return GroomerInterface
     */
    public function getDataModel()
    {
        $groomerData = $this->getData();
        
        $groomerDataObject = $this->groomerDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $groomerDataObject,
            $groomerData,
            GroomerInterface::class
        );
        
        return $groomerDataObject;
    }
}

