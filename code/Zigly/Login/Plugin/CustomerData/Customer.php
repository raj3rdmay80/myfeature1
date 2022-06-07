<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Plugin\CustomerData;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Customer
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
    * Recipient email config path
    */
    const XML_PATH_PLACEHOLDER_IMG = 'catalog/placeholder/image_placeholder';

    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param CurrentCustomer $currentCustomer
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        CurrentCustomer $currentCustomer
    ) {
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->currentCustomer = $currentCustomer;
    }

    public function afterGetSectionData(\Magento\Customer\CustomerData\Customer $subject, $result)
    {
        $customer = $this->currentCustomer->getCustomer();
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        $placeHolderImg = $mediaUrl . 'catalog/product/placeholder/' . $this->storeManager->getStore()->getConfig("catalog/placeholder/image_placeholder");
        $placeHolderImg = $mediaUrl . 'custom/zigly-profile.png';

        $result['imgpath'] = ($customer->getCustomAttribute('customerprofile_image')) ? $mediaUrl . 'customer' .$customer->getCustomAttribute('customerprofile_image')->getValue() : $placeHolderImg;
        // $result['phoneNumber'] = (!empty($customer->getCustomAttribute('phone_number'))) ? $customer->getCustomAttribute('phone_number')->getValue() : '';
        $result['entityId'] = $customer->getId();
        $wallet = is_null($customer->getCustomAttribute('wallet_balance')) ? "0" : $customer->getCustomAttribute('wallet_balance')->getValue();
        $result['wallet'] = 'Rs.'.floor($wallet); //$this->getCurrentCurrencySymbol()
        return $result;
    }

    /**
     * Get currency symbol for current locale and currency code
     *
     * @return string
     */    
    public function getCurrentCurrencySymbol()
    {
        $currencySymbol = $this->priceCurrency->getCurrencySymbol('default');
        return $currencySymbol;
    }
}
