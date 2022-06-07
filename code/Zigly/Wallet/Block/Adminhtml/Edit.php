<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Wallet\Block\Adminhtml;

use Magento\Backend\Model\Url;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Edit extends Template
{

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Url $backendUrlManager
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Url $backendUrlManager,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        CustomerRepositoryInterface $customerRepositoryInterface,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->backendUrlManager  = $backendUrlManager;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($context, $data);
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        $customerid = $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        return $customerid;
    }

    public function getAddWalletUrl()
    {
        return $this->backendUrlManager->getUrl('zigly_wallet/wallet/save');
    }

    /*
    * get total balance
    */
    public function getTotalBalance()
    {
        $customerId = $this->getCustomerId();
        $customer =$this->customerRepositoryInterface->getById($customerId);
        $totalBalance = $customer->getCustomAttribute('wallet_balance');
        $totalBalance = is_null($totalBalance) ? "0" : $totalBalance->getvalue(); 
        return $totalBalance;
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
