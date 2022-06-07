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
use Zigly\Species\Api\BreedRepositoryInterface;
use Zigly\Species\Api\Data\BreedInterfaceFactory;
use Zigly\Species\Api\Data\BreedSearchResultsInterfaceFactory;
use Zigly\Species\Model\ResourceModel\Breed as ResourceBreed;
use Zigly\Species\Model\ResourceModel\Breed\CollectionFactory as BreedCollectionFactory;

class BreedRepository implements BreedRepositoryInterface
{

    protected $dataBreedFactory;

    protected $extensibleDataObjectConverter;
    protected $breedCollectionFactory;

    protected $searchResultsFactory;

    protected $resource;

    private $storeManager;

    protected $dataObjectHelper;

    protected $breedFactory;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;


    /**
     * @param ResourceBreed $resource
     * @param BreedFactory $breedFactory
     * @param BreedInterfaceFactory $dataBreedFactory
     * @param BreedCollectionFactory $breedCollectionFactory
     * @param BreedSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceBreed $resource,
        BreedFactory $breedFactory,
        BreedInterfaceFactory $dataBreedFactory,
        BreedCollectionFactory $breedCollectionFactory,
        BreedSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->breedFactory = $breedFactory;
        $this->breedCollectionFactory = $breedCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataBreedFactory = $dataBreedFactory;
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
        \Zigly\Species\Api\Data\BreedInterface $breed
    ) {
        /* if (empty($breed->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $breed->setStoreId($storeId);
        } */
        
        $breedData = $this->extensibleDataObjectConverter->toNestedArray(
            $breed,
            [],
            \Zigly\Species\Api\Data\BreedInterface::class
        );
        
        $breedModel = $this->breedFactory->create()->setData($breedData);
        
        try {
            $this->resource->save($breedModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the breed: %1',
                $exception->getMessage()
            ));
        }
        return $breedModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($breedId)
    {
        $breed = $this->breedFactory->create();
        $this->resource->load($breed, $breedId);
        if (!$breed->getId()) {
            throw new NoSuchEntityException(__('Breed with id "%1" does not exist.', $breedId));
        }
        return $breed->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->breedCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Zigly\Species\Api\Data\BreedInterface::class
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
        \Zigly\Species\Api\Data\BreedInterface $breed
    ) {
        try {
            $breedModel = $this->breedFactory->create();
            $this->resource->load($breedModel, $breed->getBreedId());
            $this->resource->delete($breedModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Breed: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($breedId)
    {
        return $this->delete($this->get($breedId));
    }
}

