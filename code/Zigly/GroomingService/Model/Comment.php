<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zigly\GroomingService\Api\Data\CommentInterface;
use Zigly\GroomingService\Api\Data\CommentInterfaceFactory;

class Comment extends \Magento\Framework\Model\AbstractModel
{

    protected $commentDataFactory;

    protected $_eventPrefix = 'zigly_service_grooming_comment';
    protected $dataObjectHelper;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CommentInterfaceFactory $commentDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zigly\GroomingService\Model\ResourceModel\Comment $resource
     * @param \Zigly\GroomingService\Model\ResourceModel\Comment\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CommentInterfaceFactory $commentDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Zigly\GroomingService\Model\ResourceModel\Comment $resource,
        \Zigly\GroomingService\Model\ResourceModel\Comment\Collection $resourceCollection,
        array $data = []
    ) {
        $this->commentDataFactory = $commentDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve comment model with comment data
     * @return CommentInterface
     */
    public function getDataModel()
    {
        $commentData = $this->getData();
        
        $commentDataObject = $this->commentDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $commentDataObject,
            $commentData,
            CommentInterface::class
        );
        
        return $commentDataObject;
    }
}
