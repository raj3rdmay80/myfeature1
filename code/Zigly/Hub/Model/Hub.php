<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Hub
 */
declare(strict_types=1);

namespace Zigly\Hub\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zigly\Hub\Api\Data\HubInterface;
use Zigly\Hub\Api\Data\HubInterfaceFactory;

class Hub extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $hubDataFactory;

    protected $_eventPrefix = 'zigly_hub_hub';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param HubInterfaceFactory $hubDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zigly\Hub\Model\ResourceModel\Hub $resource
     * @param \Zigly\Hub\Model\ResourceModel\Hub\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        HubInterfaceFactory $hubDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Zigly\Hub\Model\ResourceModel\Hub $resource,
        \Zigly\Hub\Model\ResourceModel\Hub\Collection $resourceCollection,
        array $data = []
    ) {
        $this->hubDataFactory = $hubDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve hub model with hub data
     * @return HubInterface
     */
    public function getDataModel()
    {
        $hubData = $this->getData();
        
        $hubDataObject = $this->hubDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $hubDataObject,
            $hubData,
            HubInterface::class
        );
        
        return $hubDataObject;
    }
}

