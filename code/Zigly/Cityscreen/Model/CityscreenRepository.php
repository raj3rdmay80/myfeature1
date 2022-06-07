<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Cityscreen
 */
declare(strict_types=1);

namespace Zigly\Cityscreen\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\Cityscreen\Api\CityscreenRepositoryInterface;
use Zigly\Cityscreen\Api\Data\CityscreenInterfaceFactory;
use Zigly\Cityscreen\Api\Data\CityscreenSearchResultsInterfaceFactory;
use Zigly\Cityscreen\Model\ResourceModel\Cityscreen as ResourceCityscreen;
use Zigly\Cityscreen\Model\ResourceModel\Cityscreen\CollectionFactory as CityscreenCollectionFactory;

class CityscreenRepository implements CityscreenRepositoryInterface
{

    protected $resource;

    protected $dataCityscreenFactory;

    protected $searchResultsFactory;

    protected $extensibleDataObjectConverter;
    private $storeManager;

    protected $dataObjectHelper;

    protected $cityscreenFactory;

    protected $cityscreenCollectionFactory;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;


    /**
     * @param ResourceCityscreen $resource
     * @param CityscreenFactory $cityscreenFactory
     * @param CityscreenInterfaceFactory $dataCityscreenFactory
     * @param CityscreenCollectionFactory $cityscreenCollectionFactory
     * @param CityscreenSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceCityscreen $resource,
        CityscreenFactory $cityscreenFactory,
        CityscreenInterfaceFactory $dataCityscreenFactory,
        CityscreenCollectionFactory $cityscreenCollectionFactory,
        CityscreenSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->cityscreenFactory = $cityscreenFactory;
        $this->cityscreenCollectionFactory = $cityscreenCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCityscreenFactory = $dataCityscreenFactory;
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
        \Zigly\Cityscreen\Api\Data\CityscreenInterface $cityscreen
    ) {
        /* if (empty($cityscreen->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $cityscreen->setStoreId($storeId);
        } */
        
        $cityscreenData = $this->extensibleDataObjectConverter->toNestedArray(
            $cityscreen,
            [],
            \Zigly\Cityscreen\Api\Data\CityscreenInterface::class
        );
        
        $cityscreenModel = $this->cityscreenFactory->create()->setData($cityscreenData);
        
        try {
            $this->resource->save($cityscreenModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the cityscreen: %1',
                $exception->getMessage()
            ));
        }
        return $cityscreenModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($cityscreenId)
    {
        $cityscreen = $this->cityscreenFactory->create();
        $this->resource->load($cityscreen, $cityscreenId);
        if (!$cityscreen->getId()) {
            throw new NoSuchEntityException(__('Cityscreen with id "%1" does not exist.', $cityscreenId));
        }
        return $cityscreen->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->cityscreenCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Zigly\Cityscreen\Api\Data\CityscreenInterface::class
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
        \Zigly\Cityscreen\Api\Data\CityscreenInterface $cityscreen
    ) {
        try {
            $cityscreenModel = $this->cityscreenFactory->create();
            $this->resource->load($cityscreenModel, $cityscreen->getCityscreenId());
            $this->resource->delete($cityscreenModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Cityscreen: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($cityscreenId)
    {
        return $this->delete($this->get($cityscreenId));
    }
}

