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
use Zigly\GroomingService\Api\Data\GroomingInterfaceFactory;
use Zigly\GroomingService\Api\Data\GroomingSearchResultsInterfaceFactory;
use Zigly\GroomingService\Api\GroomingRepositoryInterface;
use Zigly\GroomingService\Model\ResourceModel\Grooming as ResourceGrooming;
use Zigly\GroomingService\Model\ResourceModel\Grooming\CollectionFactory as GroomingCollectionFactory;

class GroomingRepository implements GroomingRepositoryInterface
{

    protected $groomingFactory;

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    private $storeManager;

    protected $dataObjectHelper;

    protected $dataGroomingFactory;

    protected $groomingCollectionFactory;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;


    /**
     * @param ResourceGrooming $resource
     * @param GroomingFactory $groomingFactory
     * @param GroomingInterfaceFactory $dataGroomingFactory
     * @param GroomingCollectionFactory $groomingCollectionFactory
     * @param GroomingSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceGrooming $resource,
        GroomingFactory $groomingFactory,
        GroomingInterfaceFactory $dataGroomingFactory,
        GroomingCollectionFactory $groomingCollectionFactory,
        GroomingSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->groomingFactory = $groomingFactory;
        $this->groomingCollectionFactory = $groomingCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataGroomingFactory = $dataGroomingFactory;
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
        \Zigly\GroomingService\Api\Data\GroomingInterface $grooming
    ) {
        /* if (empty($grooming->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $grooming->setStoreId($storeId);
        } */
        
        $groomingData = $this->extensibleDataObjectConverter->toNestedArray(
            $grooming,
            [],
            \Zigly\GroomingService\Api\Data\GroomingInterface::class
        );
        
        $groomingModel = $this->groomingFactory->create()->setData($groomingData);
        
        try {
            $this->resource->save($groomingModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the grooming: %1',
                $exception->getMessage()
            ));
        }
        return $groomingModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($entityId)
    {
        $grooming = $this->groomingFactory->create();
        $this->resource->load($grooming, $entityId);
        if (!$grooming->getId()) {
            throw new NoSuchEntityException(__('Grooming with id "%1" does not exist.', $entityId));
        }
        return $grooming->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->groomingCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Zigly\GroomingService\Api\Data\GroomingInterface::class
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
        \Zigly\GroomingService\Api\Data\GroomingInterface $grooming
    ) {
        try {
            $groomingModel = $this->groomingFactory->create();
            $this->resource->load($groomingModel, $grooming->getEntityId());
            $this->resource->delete($groomingModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Grooming: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($entityId)
    {
        return $this->delete($this->get($entityId));
    }
}

