<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Block\Wallet;

use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\SessionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Unserialize\Unserialize;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Zigly\Wallet\Model\ResourceModel\Wallet\CollectionFactory;

class Index extends \Magento\Framework\View\Element\Template
{

    /**
     * @var $collectionFactory
     */
    protected $collectionFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * Constructor
     * @param Json $json
     * @param array $data
     * @param Context $context
     * @param Unserialize $unserialize
     * @param SessionFactory $customer
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $collectionFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     */
    public function __construct(
        Json $json,
        Context $context,
        Unserialize $unserialize,
        SessionFactory $customer,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        PriceCurrencyInterface $priceCurrency,
        CustomerRepositoryInterface $customerRepositoryInterface,
        array $data = []
    ) {
        $this->json = $json;
        $this->customer = $customer;
        $this->unserialize = $unserialize;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->collectionFactory = $collectionFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($context, $data);
    }

    /**
     * Convert and format price value for current application store
     *
     * @param   float $value
     * @param   bool $format
     * @param   bool $includeContainer
     * @return  float|string
     */
    public function currency($value, $format = true, $includeContainer = true)
    {
        return $format
            ? $this->priceCurrency->convertAndFormat($value, $includeContainer)
            : $this->priceCurrency->convert($value);
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getPastTransactions()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'wallet.past.transaction.pager'
            )->setCollection(
                $this->getPastTransactions()
            );
            $this->setChild('pager', $pager);
            $this->getPastTransactions()->load();
        }
        return $this;
    }

    /**
     * Get Pager child block output
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /*
    * get past transaction
    */
    public function getPastTransactions()
    {
        $customerId = $this->customer->create()->getCustomer()->getId();
        $transactionCollection = [];
        if ($customerId) {
            $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
            $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 10;
            $transactionCollection = $this->collectionFactory->create()->addFieldToFilter('customer_id', ['in' => $customerId])->addFieldToFilter('visibility', ['eq' => 1])->setOrder('wallet_id', 'DESC')->setPageSize($pageSize)->setCurPage($page);
        }
        return $transactionCollection;
    }

    /*
    * get total balance
    */
    public function getTotalBalance()
    {
        $customerId = $this->customer->create()->getCustomer()->getId();
        $customer =$this->customerRepositoryInterface->getById($customerId);
        $totalBalance = $customer->getCustomAttribute('wallet_balance');
        $totalBalance = is_null($totalBalance) ? "0" : $totalBalance->getvalue(); 
        return $totalBalance;
    }

    /*
    * get wallet money
    */
    public function getWalletMoney()
    {
        $value = $this->scopeConfig->getValue('wallet/wallet_money/add_wallet_money', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (empty($value)) {
            return false;
        }
        if ($this->isSerialized($value)) {
            $unserializer = $this->unserialize;
        } else {
            $unserializer = $this->json;
        }
        $data = $unserializer->unserialize($value);
        $reason = [];
        foreach ($data as $key => $reas) {
            if ($reas['add_wallet_money'] >= $this->getMinRecharge() && $reas['add_wallet_money'] <= $this->getMaxRecharge()) {
                $reason[] = $reas['add_wallet_money'];
            }
        }
        return $reason;
    }

    /**
     * Check if value is a serialized string
     *
     * @param string $value
     * @return boolean
     */
    private function isSerialized($value)
    {
        return (boolean) preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
    }

    public function getMinRecharge()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $store = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('wallet/wallet_recharge/minimum_recharge', $store, $storeId);
    }

    public function getMaxRecharge()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $store = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('wallet/wallet_recharge/maximum_recharge', $store, $storeId);
    }
}