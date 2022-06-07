<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Activities
 */
declare(strict_types=1);

namespace Zigly\Activities\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zigly\Activities\Api\Data\ActivitiesInterface;
use Zigly\Activities\Api\Data\ActivitiesInterfaceFactory;

class Activities extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $activitiesDataFactory;

    protected $_eventPrefix = 'zigly_activities_activities';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ActivitiesInterfaceFactory $activitiesDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zigly\Activities\Model\ResourceModel\Activities $resource
     * @param \Zigly\Activities\Model\ResourceModel\Activities\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ActivitiesInterfaceFactory $activitiesDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Zigly\Activities\Model\ResourceModel\Activities $resource,
        \Zigly\Activities\Model\ResourceModel\Activities\Collection $resourceCollection,
        array $data = []
    ) {
        $this->activitiesDataFactory = $activitiesDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve activities model with activities data
     * @return ActivitiesInterface
     */
    public function getDataModel()
    {
        $activitiesData = $this->getData();
        
        $activitiesDataObject = $this->activitiesDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $activitiesDataObject,
            $activitiesData,
            ActivitiesInterface::class
        );
        
        return $activitiesDataObject;
    }
}

