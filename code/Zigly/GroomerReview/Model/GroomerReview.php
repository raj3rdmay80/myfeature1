<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomerReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zigly\GroomerReview\Api\Data\GroomerReviewInterface;
use Zigly\GroomerReview\Api\Data\GroomerReviewInterfaceFactory;

class GroomerReview extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $GroomerReviewDataFactory;

    protected $_eventPrefix = 'zigly_groomerreview_groomerreview';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param GroomerReviewInterfaceFactory $groomerReviewDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zigly\GroomerReview\Model\ResourceModel\GroomerReview $resource
     * @param \Zigly\GroomerReview\Model\ResourceModel\GroomerReview\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        GroomerReviewInterfaceFactory $groomerReviewDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Zigly\GroomerReview\Model\ResourceModel\GroomerReview $resource,
        \Zigly\GroomerReview\Model\ResourceModel\GroomerReview\Collection $resourceCollection,
        array $data = []
    ) {
        $this->groomerReviewDataFactory = $groomerReviewDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve groomer review model with groomer review data
     * @return GroomerReviewInterface
     */
    public function getDataModel()
    {
        $groomerReviewData = $this->getData();
        
        $groomerReviewDataObject = $this->groomerReviewDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $groomerReviewDataObject,
            $groomerReviewData,
            GroomerReviewInterface::class
        );
        
        return $groomerReviewDataObject;
    }
}

