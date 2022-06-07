<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zigly\GroomingService\Api\Data\GroomingInterface;
use Zigly\GroomingService\Api\Data\GroomingInterfaceFactory;

class Grooming extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{

    const CACHE_TAG = 'zigly_service_grooming';

    /**
     * Grooming Services Status
     */
    const STATUS_PENDING = 'Pending';

    const STATUS_SCHEDULED = 'Scheduled';

    const STATUS_COMPLETED = 'Completed';

    const STATUS_INPROGRESS = 'Inprogress';

    const STATUS_CANCELLED_BY_ADMIN = 'Cancelled by Admin';

    const STATUS_CANCELLED_BY_CUSTOMER = 'Cancelled by Customer';

    const STATUS_CANCELLED_BY_PROFESSIONAL = 'Cancelled by Professional';

    const STATUS_RESCHEDULED_BY_ADMIN = 'Rescheduled by Admin';

    const STATUS_RESCHEDULED_BY_CUSTOMER = 'Rescheduled by Customer';

    const STATUS_RESCHEDULED_BY_PROFESSIONAL = 'Rescheduled by Professional';

    const STATUS_CUSTOMER_NOT_REACHABLE = 'Customer not reachable';

    const STATUS_CAN_T_DELIVER_SERVICE = 'Can’t deliver service';

    const STATUS_I_HAVE_ARRIVED = 'I have arrived';

    protected $_cacheTag = 'zigly_service_grooming';

    protected $_eventPrefix = 'zigly_service_grooming';
    protected $dataObjectHelper;

    protected $groomingDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param GroomingInterfaceFactory $groomingDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zigly\GroomingService\Model\ResourceModel\Grooming $resource
     * @param \Zigly\GroomingService\Model\ResourceModel\Grooming\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        GroomingInterfaceFactory $groomingDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Zigly\GroomingService\Model\ResourceModel\Grooming $resource,
        \Zigly\GroomingService\Model\ResourceModel\Grooming\Collection $resourceCollection,
        array $data = []
    ) {
        $this->groomingDataFactory = $groomingDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve grooming model with grooming data
     * @return GroomingInterface
     */
    public function getDataModel()
    {
        $groomingData = $this->getData();
        
        $groomingDataObject = $this->groomingDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $groomingDataObject,
            $groomingData,
            GroomingInterface::class
        );
        
        return $groomingDataObject;
    }

    protected function _construct()
    {
        $this->_init('Zigly\GroomingService\Model\ResourceModel\Grooming');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getBookingId()
    {
        return $this->getId();
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}

