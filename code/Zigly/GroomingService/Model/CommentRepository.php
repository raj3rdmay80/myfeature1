<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\GroomingService\Api\CommentRepositoryInterface;
use Zigly\GroomingService\Api\Data\CommentInterfaceFactory;
use Zigly\GroomingService\Api\Data\CommentSearchResultsInterfaceFactory;
use Zigly\GroomingService\Model\ResourceModel\Comment as ResourceComment;
use Zigly\GroomingService\Model\ResourceModel\Comment\CollectionFactory as CommentCollectionFactory;

class CommentRepository implements CommentRepositoryInterface
{

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    protected $commentFactory;

    protected $dataCommentFactory;

    protected $commentCollectionFactory;

    private $storeManager;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;


    /**
     * @param ResourceComment $resource
     * @param CommentFactory $commentFactory
     * @param CommentInterfaceFactory $dataCommentFactory
     * @param CommentCollectionFactory $commentCollectionFactory
     * @param CommentSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceComment $resource,
        CommentFactory $commentFactory,
        CommentInterfaceFactory $dataCommentFactory,
        CommentCollectionFactory $commentCollectionFactory,
        CommentSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->commentFactory = $commentFactory;
        $this->commentCollectionFactory = $commentCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCommentFactory = $dataCommentFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Zigly\GroomingService\Api\Data\CommentInterface $comment
    ) {
        /* if (empty($comment->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $comment->setStoreId($storeId);
        } */
        
        $commentData = $this->extensibleDataObjectConverter->toNestedArray(
            $comment,
            [],
            \Zigly\GroomingService\Api\Data\CommentInterface::class
        );
        
        $commentModel = $this->commentFactory->create()->setData($commentData);
        
        try {
            $this->resource->save($commentModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the comment: %1',
                $exception->getMessage()
            ));
        }
        return $commentModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($commentId)
    {
        $comment = $this->commentFactory->create();
        $this->resource->load($comment, $commentId);
        if (!$comment->getId()) {
            throw new NoSuchEntityException(__('Comment with id "%1" does not exist.', $commentId));
        }
        return $comment->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->commentCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Zigly\GroomingService\Api\Data\CommentInterface::class
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
    public function delete(
        \Zigly\GroomingService\Api\Data\CommentInterface $comment
    ) {
        try {
            $commentModel = $this->commentFactory->create();
            $this->resource->load($commentModel, $comment->getCommentId());
            $this->resource->delete($commentModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Comment: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($commentId)
    {
        return $this->delete($this->get($commentId));
    }
}
