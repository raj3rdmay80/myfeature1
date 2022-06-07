<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Cityscreen
 */
declare(strict_types=1);

namespace Zigly\Cityscreen\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zigly\Cityscreen\Api\Data\CityscreenInterface;
use Zigly\Cityscreen\Api\Data\CityscreenInterfaceFactory;

class Cityscreen extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'zigly_cityscreen_cityscreen';
    protected $dataObjectHelper;

    protected $cityscreenDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CityscreenInterfaceFactory $cityscreenDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zigly\Cityscreen\Model\ResourceModel\Cityscreen $resource
     * @param \Zigly\Cityscreen\Model\ResourceModel\Cityscreen\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CityscreenInterfaceFactory $cityscreenDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Zigly\Cityscreen\Model\ResourceModel\Cityscreen $resource,
        \Zigly\Cityscreen\Model\ResourceModel\Cityscreen\Collection $resourceCollection,
        array $data = []
    ) {
        $this->cityscreenDataFactory = $cityscreenDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve cityscreen model with cityscreen data
     * @return CityscreenInterface
     */
    public function getDataModel()
    {
        $cityscreenData = $this->getData();
        
        $cityscreenDataObject = $this->cityscreenDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $cityscreenDataObject,
            $cityscreenData,
            CityscreenInterface::class
        );
        
        return $cityscreenDataObject;
    }
}

