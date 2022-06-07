<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_CouponService
 */
declare(strict_types=1);

namespace Zigly\CouponService\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zigly\CouponService\Api\Data\CouponServiceInterface;
use Zigly\CouponService\Api\Data\CouponServiceInterfaceFactory;

class CouponService extends \Magento\Framework\Model\AbstractModel
{

    protected $couponserviceDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'zigly_couponservice_couponservice';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CouponServiceInterfaceFactory $couponserviceDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zigly\CouponService\Model\ResourceModel\CouponService $resource
     * @param \Zigly\CouponService\Model\ResourceModel\CouponService\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CouponServiceInterfaceFactory $couponserviceDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Zigly\CouponService\Model\ResourceModel\CouponService $resource,
        \Zigly\CouponService\Model\ResourceModel\CouponService\Collection $resourceCollection,
        array $data = []
    ) {
        $this->couponserviceDataFactory = $couponserviceDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve couponservice model with couponservice data
     * @return CouponServiceInterface
     */
    public function getDataModel()
    {
        $couponserviceData = $this->getData();
        
        $couponserviceDataObject = $this->couponserviceDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $couponserviceDataObject,
            $couponserviceData,
            CouponServiceInterface::class
        );
        
        return $couponserviceDataObject;
    }
}

