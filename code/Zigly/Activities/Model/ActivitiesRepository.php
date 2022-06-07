<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Activities
 */
declare(strict_types=1);

namespace Zigly\Activities\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\Activities\Api\ActivitiesRepositoryInterface;
use Zigly\Activities\Api\Data\ActivitiesInterfaceFactory;
use Zigly\Activities\Api\Data\ActivitiesSearchResultsInterfaceFactory;
use Zigly\Activities\Model\ResourceModel\Activities as ResourceActivities;
use Zigly\Activities\Model\ResourceModel\Activities\CollectionFactory as ActivitiesCollectionFactory;

class ActivitiesRepository implements ActivitiesRepositoryInterface
{

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    private $collectionProcessor;

    private $storeManager;

    protected $dataObjectHelper;

    protected $dataActivitiesFactory;

    protected $dataObjectProcessor;

    protected $activitiesCollectionFactory;

    protected $extensionAttributesJoinProcessor;

    protected $activitiesFactory;


    /**
     * @param ResourceActivities $resource
     * @param ActivitiesFactory $activitiesFactory
     * @param ActivitiesInterfaceFactory $dataActivitiesFactory
     * @param ActivitiesCollectionFactory $activitiesCollectionFactory
     * @param ActivitiesSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceActivities $resource,
        ActivitiesFactory $activitiesFactory,
        ActivitiesInterfaceFactory $dataActivitiesFactory,
        ActivitiesCollectionFactory $activitiesCollectionFactory,
        ActivitiesSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->activitiesFactory = $activitiesFactory;
        $this->activitiesCollectionFactory = $activitiesCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataActivitiesFactory = $dataActivitiesFactory;
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
        \Zigly\Activities\Api\Data\ActivitiesInterface $activities
    ) {
        /* if (empty($activities->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $activities->setStoreId($storeId);
        } */
        
        $activitiesData = $this->extensibleDataObjectConverter->toNestedArray(
            $activities,
            [],
            \Zigly\Activities\Api\Data\ActivitiesInterface::class
        );
        
        $activitiesModel = $this->activitiesFactory->create()->setData($activitiesData);
        
        try {
            $this->resource->save($activitiesModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the activities: %1',
                $exception->getMessage()
            ));
        }
        return $activitiesModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($activitiesId)
    {
        $activities = $this->activitiesFactory->create();
        $this->resource->load($activities, $activitiesId);
        if (!$activities->getId()) {
            throw new NoSuchEntityException(__('Activities with id "%1" does not exist.', $activitiesId));
        }
        return $activities->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->activitiesCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Zigly\Activities\Api\Data\ActivitiesInterface::class
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
        \Zigly\Activities\Api\Data\ActivitiesInterface $activities
    ) {
        try {
            $activitiesModel = $this->activitiesFactory->create();
            $this->resource->load($activitiesModel, $activities->getActivitiesId());
            $this->resource->delete($activitiesModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Activities: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($activitiesId)
    {
        return $this->delete($this->get($activitiesId));
    }
}

