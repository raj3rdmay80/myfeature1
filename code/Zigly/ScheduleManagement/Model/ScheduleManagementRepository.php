<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\ScheduleManagement\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterfaceFactory;
use Zigly\ScheduleManagement\Api\Data\ScheduleManagementSearchResultsInterfaceFactory;
use Zigly\ScheduleManagement\Api\ScheduleManagementRepositoryInterface;
use Zigly\ScheduleManagement\Model\ResourceModel\ScheduleManagement as ResourceScheduleManagement;
use Zigly\ScheduleManagement\Model\ResourceModel\ScheduleManagement\CollectionFactory as ScheduleManagementCollectionFactory;

class ScheduleManagementRepository implements ScheduleManagementRepositoryInterface
{

    protected $resource;

    protected $scheduleManagementFactory;

    protected $scheduleManagementCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataScheduleManagementFactory;

    protected $extensionAttributesJoinProcessor;

    private $storeManager;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceScheduleManagement $resource
     * @param ScheduleManagementFactory $scheduleManagementFactory
     * @param ScheduleManagementInterfaceFactory $dataScheduleManagementFactory
     * @param ScheduleManagementCollectionFactory $scheduleManagementCollectionFactory
     * @param ScheduleManagementSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceScheduleManagement $resource,
        ScheduleManagementFactory $scheduleManagementFactory,
        ScheduleManagementInterfaceFactory $dataScheduleManagementFactory,
        ScheduleManagementCollectionFactory $scheduleManagementCollectionFactory,
        ScheduleManagementSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->scheduleManagementFactory = $scheduleManagementFactory;
        $this->scheduleManagementCollectionFactory = $scheduleManagementCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataScheduleManagementFactory = $dataScheduleManagementFactory;
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
        \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface $scheduleManagement
    ) {
        /* if (empty($scheduleManagement->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $scheduleManagement->setStoreId($storeId);
        } */
        
        $scheduleManagementData = $this->extensibleDataObjectConverter->toNestedArray(
            $scheduleManagement,
            [],
            \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface::class
        );
        
        $scheduleManagementModel = $this->scheduleManagementFactory->create()->setData($scheduleManagementData);
        
        try {
            $this->resource->save($scheduleManagementModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the scheduleManagement: %1',
                $exception->getMessage()
            ));
        }
        return $scheduleManagementModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($scheduleManagementId)
    {
        $scheduleManagement = $this->scheduleManagementFactory->create();
        $this->resource->load($scheduleManagement, $scheduleManagementId);
        if (!$scheduleManagement->getId()) {
            throw new NoSuchEntityException(__('ScheduleManagement with id "%1" does not exist.', $scheduleManagementId));
        }
        return $scheduleManagement->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->scheduleManagementCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface::class
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
        \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface $scheduleManagement
    ) {
        try {
            $scheduleManagementModel = $this->scheduleManagementFactory->create();
            $this->resource->load($scheduleManagementModel, $scheduleManagement->getSchedulemanagementId());
            $this->resource->delete($scheduleManagementModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the ScheduleManagement: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($scheduleManagementId)
    {
        return $this->delete($this->get($scheduleManagementId));
    }
}

