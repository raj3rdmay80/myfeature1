<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_CouponService
 */
declare(strict_types=1);

namespace Zigly\CouponService\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\CouponService\Api\CouponServiceRepositoryInterface;
use Zigly\CouponService\Api\Data\CouponServiceInterfaceFactory;
use Zigly\CouponService\Api\Data\CouponServiceSearchResultsInterfaceFactory;
use Zigly\CouponService\Model\ResourceModel\CouponService as ResourceCouponService;
use Zigly\CouponService\Model\ResourceModel\CouponService\CollectionFactory as CouponServiceCollectionFactory;

class CouponServiceRepository implements CouponServiceRepositoryInterface
{

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    protected $couponServiceCollectionFactory;

    private $storeManager;

    protected $dataObjectHelper;

    protected $dataCouponServiceFactory;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;

    protected $couponServiceFactory;


    /**
     * @param ResourceCouponService $resource
     * @param CouponServiceFactory $couponServiceFactory
     * @param CouponServiceInterfaceFactory $dataCouponServiceFactory
     * @param CouponServiceCollectionFactory $couponServiceCollectionFactory
     * @param CouponServiceSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceCouponService $resource,
        CouponServiceFactory $couponServiceFactory,
        CouponServiceInterfaceFactory $dataCouponServiceFactory,
        CouponServiceCollectionFactory $couponServiceCollectionFactory,
        CouponServiceSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->couponServiceFactory = $couponServiceFactory;
        $this->couponServiceCollectionFactory = $couponServiceCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCouponServiceFactory = $dataCouponServiceFactory;
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
        \Zigly\CouponService\Api\Data\CouponServiceInterface $couponService
    ) {
        /* if (empty($couponService->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $couponService->setStoreId($storeId);
        } */
        
        $couponServiceData = $this->extensibleDataObjectConverter->toNestedArray(
            $couponService,
            [],
            \Zigly\CouponService\Api\Data\CouponServiceInterface::class
        );
        
        $couponServiceModel = $this->couponServiceFactory->create()->setData($couponServiceData);
        
        try {
            $this->resource->save($couponServiceModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the couponService: %1',
                $exception->getMessage()
            ));
        }
        return $couponServiceModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($couponServiceId)
    {
        $couponService = $this->couponServiceFactory->create();
        $this->resource->load($couponService, $couponServiceId);
        if (!$couponService->getId()) {
            throw new NoSuchEntityException(__('CouponService with id "%1" does not exist.', $couponServiceId));
        }
        return $couponService->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->couponServiceCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Zigly\CouponService\Api\Data\CouponServiceInterface::class
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
        \Zigly\CouponService\Api\Data\CouponServiceInterface $couponService
    ) {
        try {
            $couponServiceModel = $this->couponServiceFactory->create();
            $this->resource->load($couponServiceModel, $couponService->getCouponserviceId());
            $this->resource->delete($couponServiceModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the CouponService: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($couponServiceId)
    {
        return $this->delete($this->get($couponServiceId));
    }
}

