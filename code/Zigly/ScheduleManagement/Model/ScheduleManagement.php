<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\ScheduleManagement\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface;
use Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterfaceFactory;

class ScheduleManagement extends \Magento\Framework\Model\AbstractModel
{

    protected $schedulemanagementDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'zigly_schedulemanagement_schedulemanagement';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ScheduleManagementInterfaceFactory $schedulemanagementDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zigly\ScheduleManagement\Model\ResourceModel\ScheduleManagement $resource
     * @param \Zigly\ScheduleManagement\Model\ResourceModel\ScheduleManagement\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ScheduleManagementInterfaceFactory $schedulemanagementDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Zigly\ScheduleManagement\Model\ResourceModel\ScheduleManagement $resource,
        \Zigly\ScheduleManagement\Model\ResourceModel\ScheduleManagement\Collection $resourceCollection,
        array $data = []
    ) {
        $this->schedulemanagementDataFactory = $schedulemanagementDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init("\Zigly\ScheduleManagement\Model\ResourceModel\ScheduleManagement");
    }

    /**
     * Retrieve schedulemanagement model with schedulemanagement data
     * @return ScheduleManagementInterface
     */
    public function getDataModel()
    {
        $schedulemanagementData = $this->getData();
        
        $schedulemanagementDataObject = $this->schedulemanagementDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $schedulemanagementDataObject,
            $schedulemanagementData,
            ScheduleManagementInterface::class
        );
        
        return $schedulemanagementDataObject;
    }
}

