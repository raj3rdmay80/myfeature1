<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Species\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\Species\Api\Data\SpeciesInterfaceFactory;
use Zigly\Species\Api\Data\SpeciesSearchResultsInterfaceFactory;
use Zigly\Species\Api\SpeciesRepositoryInterface;
use Zigly\Species\Model\ResourceModel\Species as ResourceSpecies;
use Zigly\Species\Model\ResourceModel\Species\CollectionFactory as SpeciesCollectionFactory;

class SpeciesRepository implements SpeciesRepositoryInterface
{

    protected $dataSpeciesFactory;

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    protected $speciesFactory;

    protected $speciesCollectionFactory;

    private $storeManager;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;


    /**
     * @param ResourceSpecies $resource
     * @param SpeciesFactory $speciesFactory
     * @param SpeciesInterfaceFactory $dataSpeciesFactory
     * @param SpeciesCollectionFactory $speciesCollectionFactory
     * @param SpeciesSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceSpecies $resource,
        SpeciesFactory $speciesFactory,
        SpeciesInterfaceFactory $dataSpeciesFactory,
        SpeciesCollectionFactory $speciesCollectionFactory,
        SpeciesSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->speciesFactory = $speciesFactory;
        $this->speciesCollectionFactory = $speciesCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataSpeciesFactory = $dataSpeciesFactory;
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
        \Zigly\Species\Api\Data\SpeciesInterface $species
    ) {
        /* if (empty($species->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $species->setStoreId($storeId);
        } */
        
        $speciesData = $this->extensibleDataObjectConverter->toNestedArray(
            $species,
            [],
            \Zigly\Species\Api\Data\SpeciesInterface::class
        );
        
        $speciesModel = $this->speciesFactory->create()->setData($speciesData);
        
        try {
            $this->resource->save($speciesModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the species: %1',
                $exception->getMessage()
            ));
        }
        return $speciesModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($speciesId)
    {
        $species = $this->speciesFactory->create();
        $this->resource->load($species, $speciesId);
        if (!$species->getId()) {
            throw new NoSuchEntityException(__('Species with id "%1" does not exist.', $speciesId));
        }
        return $species->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->speciesCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Zigly\Species\Api\Data\SpeciesInterface::class
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
        \Zigly\Species\Api\Data\SpeciesInterface $species
    ) {
        try {
            $speciesModel = $this->speciesFactory->create();
            $this->resource->load($speciesModel, $species->getSpeciesId());
            $this->resource->delete($speciesModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Species: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($speciesId)
    {
        return $this->delete($this->get($speciesId));
    }
}

