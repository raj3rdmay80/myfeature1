<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Hub
 */
declare(strict_types=1);

namespace Zigly\Hub\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\Hub\Api\Data\HubInterfaceFactory;
use Zigly\Hub\Api\Data\HubSearchResultsInterfaceFactory;
use Zigly\Hub\Api\HubRepositoryInterface;
use Zigly\Hub\Model\ResourceModel\Hub as ResourceHub;
use Zigly\Hub\Model\ResourceModel\Hub\CollectionFactory as HubCollectionFactory;

class HubRepository implements HubRepositoryInterface
{

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    protected $hubCollectionFactory;

    private $storeManager;

    protected $dataObjectHelper;

    protected $dataHubFactory;

    protected $dataObjectProcessor;

    protected $hubFactory;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;


    /**
     * @param ResourceHub $resource
     * @param HubFactory $hubFactory
     * @param HubInterfaceFactory $dataHubFactory
     * @param HubCollectionFactory $hubCollectionFactory
     * @param HubSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceHub $resource,
        HubFactory $hubFactory,
        HubInterfaceFactory $dataHubFactory,
        HubCollectionFactory $hubCollectionFactory,
        HubSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->hubFactory = $hubFactory;
        $this->hubCollectionFactory = $hubCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataHubFactory = $dataHubFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Zigly\Hub\Api\Data\HubInterface $hub)
    {
        /* if (empty($hub->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $hub->setStoreId($storeId);
        } */
        
        $hubData = $this->extensibleDataObjectConverter->toNestedArray(
            $hub,
            [],
            \Zigly\Hub\Api\Data\HubInterface::class
        );
        
        $hubModel = $this->hubFactory->create()->setData($hubData);
        
        try {
            $this->resource->save($hubModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the hub: %1',
                $exception->getMessage()
            ));
        }
        return $hubModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($hubId)
    {
        $hub = $this->hubFactory->create();
        $this->resource->load($hub, $hubId);
        if (!$hub->getId()) {
            throw new NoSuchEntityException(__('Hub with id "%1" does not exist.', $hubId));
        }
        return $hub->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->hubCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Zigly\Hub\Api\Data\HubInterface::class
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
    public function delete(\Zigly\Hub\Api\Data\HubInterface $hub)
    {
        try {
            $hubModel = $this->hubFactory->create();
            $this->resource->load($hubModel, $hub->getHubId());
            $this->resource->delete($hubModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Hub: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($hubId)
    {
        return $this->delete($this->get($hubId));
    }
}

