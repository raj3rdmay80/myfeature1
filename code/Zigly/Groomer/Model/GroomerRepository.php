<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Groomer
 */
declare(strict_types=1);

namespace Zigly\Groomer\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\Groomer\Api\Data\GroomerInterfaceFactory;
use Zigly\Groomer\Api\Data\GroomerSearchResultsInterfaceFactory;
use Zigly\Groomer\Api\GroomerRepositoryInterface;
use Zigly\Groomer\Model\ResourceModel\Groomer as ResourceGroomer;
use Zigly\Groomer\Model\ResourceModel\Groomer\CollectionFactory as GroomerCollectionFactory;

class GroomerRepository implements GroomerRepositoryInterface
{

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    protected $groomerFactory;

    protected $groomerCollectionFactory;

    protected $dataGroomerFactory;

    private $storeManager;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;


    /**
     * @param ResourceGroomer $resource
     * @param GroomerFactory $groomerFactory
     * @param GroomerInterfaceFactory $dataGroomerFactory
     * @param GroomerCollectionFactory $groomerCollectionFactory
     * @param GroomerSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceGroomer $resource,
        GroomerFactory $groomerFactory,
        GroomerInterfaceFactory $dataGroomerFactory,
        GroomerCollectionFactory $groomerCollectionFactory,
        GroomerSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->groomerFactory = $groomerFactory;
        $this->groomerCollectionFactory = $groomerCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataGroomerFactory = $dataGroomerFactory;
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
        \Zigly\Groomer\Api\Data\GroomerInterface $groomer
    ) {
        /* if (empty($groomer->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $groomer->setStoreId($storeId);
        } */
        
        $groomerData = $this->extensibleDataObjectConverter->toNestedArray(
            $groomer,
            [],
            \Zigly\Groomer\Api\Data\GroomerInterface::class
        );
        
        $groomerModel = $this->groomerFactory->create()->setData($groomerData);
        
        try {
            $this->resource->save($groomerModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the groomer: %1',
                $exception->getMessage()
            ));
        }
        return $groomerModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($groomerId)
    {
        $groomer = $this->groomerFactory->create();
        $this->resource->load($groomer, $groomerId);
        if (!$groomer->getId()) {
            throw new NoSuchEntityException(__('Groomer with id "%1" does not exist.', $groomerId));
        }
        return $groomer->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->groomerCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Zigly\Groomer\Api\Data\GroomerInterface::class
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
        \Zigly\Groomer\Api\Data\GroomerInterface $groomer
    ) {
        try {
            $groomerModel = $this->groomerFactory->create();
            $this->resource->load($groomerModel, $groomer->getGroomerId());
            $this->resource->delete($groomerModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Groomer: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($groomerId)
    {
        return $this->delete($this->get($groomerId));
    }
}

