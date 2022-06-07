<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zigly\ReviewTag\Api\Data\ReviewTagInterface;
use Zigly\ReviewTag\Api\Data\ReviewTagInterfaceFactory;

class ReviewTag extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $reviewTagDataFactory;

    protected $_eventPrefix = 'zigly_reviewtag_reviewtag';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ReviewTagInterfaceFactory $reviewTagDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zigly\ReviewTag\Model\ResourceModel\ReviewTag $resource
     * @param \Zigly\ReviewTag\Model\ResourceModel\ReviewTag\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ReviewTagInterfaceFactory $reviewTagDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Zigly\ReviewTag\Model\ResourceModel\ReviewTag $resource,
        \Zigly\ReviewTag\Model\ResourceModel\ReviewTag\Collection $resourceCollection,
        array $data = []
    ) {
        $this->reviewTagDataFactory = $reviewTagDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve review tag model with review tag data
     * @return ReviewTagInterface
     */
    public function getDataModel()
    {
        $reviewTagData = $this->getData();
        
        $reviewTagDataObject = $this->reviewTagDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $reviewTagDataObject,
            $reviewTagData,
            ReviewTagInterface::class
        );
        
        return $reviewTagDataObject;
    }
}

