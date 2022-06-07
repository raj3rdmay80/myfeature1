<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zigly\Wallet\Api\Data\WalletInterface;
use Zigly\Wallet\Api\Data\WalletInterfaceFactory;

class Wallet extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'zigly_wallet_wallet';
    protected $walletDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param WalletInterfaceFactory $walletDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zigly\Wallet\Model\ResourceModel\Wallet $resource
     * @param \Zigly\Wallet\Model\ResourceModel\Wallet\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        WalletInterfaceFactory $walletDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Zigly\Wallet\Model\ResourceModel\Wallet $resource,
        \Zigly\Wallet\Model\ResourceModel\Wallet\Collection $resourceCollection,
        array $data = []
    ) {
        $this->walletDataFactory = $walletDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve wallet model with wallet data
     * @return WalletInterface
     */
    public function getDataModel()
    {
        $walletData = $this->getData();
        
        $walletDataObject = $this->walletDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $walletDataObject,
            $walletData,
            WalletInterface::class
        );
        
        return $walletDataObject;
    }
}

