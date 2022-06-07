<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomerReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\GroomerReview\Api\Data\GroomerReviewInterfaceFactory;
use Zigly\GroomerReview\Api\Data\GroomerReviewSearchResultsInterfaceFactory;
use Zigly\GroomerReview\Api\GroomerReviewRepositoryInterface;
use Zigly\GroomerReview\Model\ResourceModel\GroomerReview as ResourceGroomerReview;
use Zigly\GroomerReview\Model\ResourceModel\GroomerReview\CollectionFactory as GroomerReviewCollectionFactory;

class GroomerReviewRepository implements GroomerReviewRepositoryInterface
{

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    protected $groomerReviewCollectionFactory;

    private $storeManager;

    protected $dataObjectHelper;

    protected $dataGroomerReviewFactory;

    protected $dataObjectProcessor;

    protected $groomerReviewFactory;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;


    /**
     * @param ResourceGroomerReview $resource
     * @param GroomerReviewFactory $groomerReviewFactory
     * @param GroomerReviewInterfaceFactory $dataGroomerReviewFactory
     * @param GroomerReviewCollectionFactory $groomerReviewCollectionFactory
     * @param GroomerReviewSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceGroomerReview $resource,
        GroomerReviewFactory $groomerReviewFactory,
        GroomerReviewInterfaceFactory $dataGroomerReviewFactory,
        GroomerReviewCollectionFactory $groomerReviewCollectionFactory,
        GroomerReviewSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->groomerReviewFactory = $groomerReviewFactory;
        $this->groomerReviewCollectionFactory = $groomerReviewCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataGroomerReviewFactory = $dataGroomerReviewFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Zigly\GroomerReview\Api\Data\GroomerReviewInterface $groomerReview)
    {

        $groomerReviewData = $this->extensibleDataObjectConverter->toNestedArray(
            $groomerReview,
            [],
            \Zigly\GroomerReview\Api\Data\GroomerReviewInterface::class
        );
        
        $groomerReviewModel = $this->groomerReviewFactory->create()->setData($groomerReviewData);
        
        try {
            $this->resource->save($groomerReviewModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the groomer review : %1',
                $exception->getMessage()
            ));
        }
        return $groomerReviewModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($groomerReviewId)
    {
        $groomerReview = $this->groomerReviewFactory->create();
        $this->resource->load($groomerReview, $groomerReviewId);
        if (!$groomerReview->getId()) {
            throw new NoSuchEntityException(__('Groomer Review with id "%1" does not exist.', $groomerReviewId));
        }
        return $groomerReview->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->groomerReviewCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Zigly\GroomerReview\Api\Data\GroomerReviewInterface::class
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
    public function delete(\Zigly\GroomerReview\Api\Data\GroomerReviewInterface $GroomerReview)
    {
        try {
            $groomerReviewModel = $this->groomerReviewFactory->create();
            $this->resource->load($groomerReviewModel, $groomerReview->getGroomerReviewId());
            $this->resource->delete($groomerReviewModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Groomer Review: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($groomerReviewId)
    {
        return $this->delete($this->get($groomerReviewId));
    }
}

