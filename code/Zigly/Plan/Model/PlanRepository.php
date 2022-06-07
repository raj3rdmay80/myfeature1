<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Plan
 */
declare(strict_types=1);

namespace Zigly\Plan\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\Plan\Api\Data\PlanInterfaceFactory;
use Zigly\Plan\Api\Data\PlanSearchResultsInterfaceFactory;
use Zigly\Plan\Api\PlanRepositoryInterface;
use Zigly\Plan\Model\ResourceModel\Plan as ResourcePlan;
use Zigly\Plan\Model\ResourceModel\Plan\CollectionFactory as PlanCollectionFactory;

class PlanRepository implements PlanRepositoryInterface
{

    protected $resource;

    protected $planCollectionFactory;

    protected $searchResultsFactory;

    protected $dataPlanFactory;

    protected $extensibleDataObjectConverter;
    private $storeManager;

    protected $planFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;


    /**
     * @param ResourcePlan $resource
     * @param PlanFactory $planFactory
     * @param PlanInterfaceFactory $dataPlanFactory
     * @param PlanCollectionFactory $planCollectionFactory
     * @param PlanSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourcePlan $resource,
        PlanFactory $planFactory,
        PlanInterfaceFactory $dataPlanFactory,
        PlanCollectionFactory $planCollectionFactory,
        PlanSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->planFactory = $planFactory;
        $this->planCollectionFactory = $planCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPlanFactory = $dataPlanFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Zigly\Plan\Api\Data\PlanInterface $plan)
    {
        /* if (empty($plan->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $plan->setStoreId($storeId);
        } */
        
        $planData = $this->extensibleDataObjectConverter->toNestedArray(
            $plan,
            [],
            \Zigly\Plan\Api\Data\PlanInterface::class
        );
        
        $planModel = $this->planFactory->create()->setData($planData);
        
        try {
            $this->resource->save($planModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the plan: %1',
                $exception->getMessage()
            ));
        }
        return $planModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($planId)
    {
        $plan = $this->planFactory->create();
        $this->resource->load($plan, $planId);
        if (!$plan->getId()) {
            throw new NoSuchEntityException(__('Plan with id "%1" does not exist.', $planId));
        }
        return $plan->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->planCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Zigly\Plan\Api\Data\PlanInterface::class
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
    public function delete(\Zigly\Plan\Api\Data\PlanInterface $plan)
    {
        try {
            $planModel = $this->planFactory->create();
            $this->resource->load($planModel, $plan->getPlanId());
            $this->resource->delete($planModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Plan: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($planId)
    {
        return $this->delete($this->get($planId));
    }
}

