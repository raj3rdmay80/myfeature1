<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\ReviewTag\Api\Data\ReviewTagInterfaceFactory;
use Zigly\ReviewTag\Api\Data\ReviewTagSearchResultsInterfaceFactory;
use Zigly\ReviewTag\Api\ReviewTagRepositoryInterface;
use Zigly\ReviewTag\Model\ResourceModel\ReviewTag as ResourceReviewTag;
use Zigly\ReviewTag\Model\ResourceModel\ReviewTag\CollectionFactory as ReviewTagCollectionFactory;

class ReviewTagRepository implements ReviewTagRepositoryInterface
{

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    protected $reviewTagCollectionFactory;

    private $storeManager;

    protected $dataObjectHelper;

    protected $dataReviewTagFactory;

    protected $dataObjectProcessor;

    protected $reviewTagFactory;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;


    /**
     * @param ResourceReviewTag $resource
     * @param ReviewTagFactory $reviewTagFactory
     * @param ReviewTagInterfaceFactory $dataReviewTagFactory
     * @param ReviewTagCollectionFactory $reviewTagCollectionFactory
     * @param ReviewTagSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceReviewTag $resource,
        ReviewTagFactory $reviewTagFactory,
        ReviewTagInterfaceFactory $dataReviewTagFactory,
        ReviewTagCollectionFactory $reviewTagCollectionFactory,
        ReviewTagSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->reviewTagFactory = $reviewTagFactory;
        $this->reviewTagCollectionFactory = $reviewTagCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataReviewTagFactory = $dataReviewTagFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Zigly\ReviewTag\Api\Data\ReviewTagInterface $reviewTag)
    {

        $reviewTagData = $this->extensibleDataObjectConverter->toNestedArray(
            $reviewTag,
            [],
            \Zigly\ReviewTag\Api\Data\ReviewTagInterface::class
        );
        
        $reviewTagModel = $this->reviewTagFactory->create()->setData($reviewTagData);
        
        try {
            $this->resource->save($reviewTagModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the review tag: %1',
                $exception->getMessage()
            ));
        }
        return $reviewTagModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($reviewTagId)
    {
        $reviewTag = $this->reviewTagFactory->create();
        $this->resource->load($reviewTag, $reviewTagId);
        if (!$reviewTag->getId()) {
            throw new NoSuchEntityException(__('reviewTag with id "%1" does not exist.', $reviewTagId));
        }
        return $reviewTag->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->reviewTagCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Zigly\ReviewTag\Api\Data\ReviewTagInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Zigly\ReviewTag\Api\Data\ReviewTagInterface $reviewTag)
    {
        try {
            $reviewTagModel = $this->reviewTagFactory->create();
            $this->resource->load($reviewTagModel, $reviewTag->getReviewTagId());
            $this->resource->delete($reviewTagModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the reviewTag: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($reviewTagId)
    {
        return $this->delete($this->get($reviewTagId));
    }
}

