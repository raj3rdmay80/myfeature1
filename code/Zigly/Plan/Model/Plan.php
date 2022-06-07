<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Plan
 */
declare(strict_types=1);

namespace Zigly\Plan\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zigly\Plan\Api\Data\PlanInterface;
use Zigly\Plan\Api\Data\PlanInterfaceFactory;

class Plan extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $planDataFactory;

    protected $_eventPrefix = 'zigly_plan_plan';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PlanInterfaceFactory $planDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zigly\Plan\Model\ResourceModel\Plan $resource
     * @param \Zigly\Plan\Model\ResourceModel\Plan\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        PlanInterfaceFactory $planDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Zigly\Plan\Model\ResourceModel\Plan $resource,
        \Zigly\Plan\Model\ResourceModel\Plan\Collection $resourceCollection,
        array $data = []
    ) {
        $this->planDataFactory = $planDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve plan model with plan data
     * @return PlanInterface
     */
    public function getDataModel()
    {
        $planData = $this->getData();
        
        $planDataObject = $this->planDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $planDataObject,
            $planData,
            PlanInterface::class
        );
        
        return $planDataObject;
    }
}

