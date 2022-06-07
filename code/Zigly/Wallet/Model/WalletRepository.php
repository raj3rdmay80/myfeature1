<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\Wallet\Api\Data\WalletInterfaceFactory;
use Zigly\Wallet\Api\Data\WalletSearchResultsInterfaceFactory;
use Zigly\Wallet\Api\WalletRepositoryInterface;
use Zigly\Wallet\Model\ResourceModel\Wallet as ResourceWallet;
use Zigly\Wallet\Model\ResourceModel\Wallet\CollectionFactory as WalletCollectionFactory;

class WalletRepository implements WalletRepositoryInterface
{

    protected $walletCollectionFactory;

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    private $storeManager;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;

    protected $dataWalletFactory;

    protected $walletFactory;


    /**
     * @param ResourceWallet $resource
     * @param WalletFactory $walletFactory
     * @param WalletInterfaceFactory $dataWalletFactory
     * @param WalletCollectionFactory $walletCollectionFactory
     * @param WalletSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceWallet $resource,
        WalletFactory $walletFactory,
        WalletInterfaceFactory $dataWalletFactory,
        WalletCollectionFactory $walletCollectionFactory,
        WalletSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->walletFactory = $walletFactory;
        $this->walletCollectionFactory = $walletCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataWalletFactory = $dataWalletFactory;
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
        \Zigly\Wallet\Api\Data\WalletInterface $wallet
    ) {
        /* if (empty($wallet->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $wallet->setStoreId($storeId);
        } */
        
        $walletData = $this->extensibleDataObjectConverter->toNestedArray(
            $wallet,
            [],
            \Zigly\Wallet\Api\Data\WalletInterface::class
        );
        
        $walletModel = $this->walletFactory->create()->setData($walletData);
        
        try {
            $this->resource->save($walletModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the wallet: %1',
                $exception->getMessage()
            ));
        }
        return $walletModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($walletId)
    {
        $wallet = $this->walletFactory->create();
        $this->resource->load($wallet, $walletId);
        if (!$wallet->getId()) {
            throw new NoSuchEntityException(__('Wallet with id "%1" does not exist.', $walletId));
        }
        return $wallet->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->walletCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Zigly\Wallet\Api\Data\WalletInterface::class
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
        \Zigly\Wallet\Api\Data\WalletInterface $wallet
    ) {
        try {
            $walletModel = $this->walletFactory->create();
            $this->resource->load($walletModel, $wallet->getWalletId());
            $this->resource->delete($walletModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Wallet: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($walletId)
    {
        return $this->delete($this->get($walletId));
    }
}

