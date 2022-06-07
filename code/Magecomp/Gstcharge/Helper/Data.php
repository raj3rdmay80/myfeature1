<?php

namespace Magecomp\Gstcharge\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Backend\Model\Session\Quote;

class Data extends AbstractHelper
{
    const CONFIG_CUSTOM_IS_ENABLED = 'Gstcharge/Gstcharge/status';
    const CONFIG_QR_ENABLED = 'Gstcharge/Gstcharge/qrstatus';
    const CONFIG_GST_TAXPER = 'Gstcharge/Gstcharge/tax_percent';
    const CONFIG_GST_TAXPER_MIN_PRICE = 'Gstcharge/Gstcharge/tax_percent_minprice';
    const CONFIG_GST_MIN_PRICE = 'Gstcharge/Gstcharge/tax_minprice';
    const CONFIG_GST_STATE = 'Gstcharge/Gstcharge/state';
    const CONFIG_GST_NUMBER = 'Gstcharge/Gstcharge/gstnumber';
    const CONFIG_PAN_NUMBER = 'Gstcharge/Gstcharge/pannumber';
    const CONFIG_CIN_NUMBER = 'Gstcharge/Gstcharge/cinnumber';
    const CONFIG_GST_TAXTYPE = 'Gstcharge/Gstcharge/taxtype';
    const CONFIG_GST_SIGNATURE = 'Gstcharge/Gstcharge/authentication';
    const CONFIG_GST_SIGNATURETEXT = 'Gstcharge/Gstcharge/signaturetext';
    const CONFIG_GST_ONSHIPPING = 'Gstcharge/ShippingGstchargeConfig/shippingchargeinclude';
    const CONFIG_GST_SHIPPING_TAXTYPE = 'Gstcharge/ShippingGstchargeConfig/taxtype';
    const CONFIG_GST_BUYERGST = 'Gstcharge/Gstcharge/buyergst';


    protected $_productloader;
    protected $_checkoutSession;
    protected $backendQuoteSession;

    public function __construct(
        Context $context,
        ProductFactory $_productloader,
        CategoryFactory $categoryFactory,
        CheckoutSession $checkoutSession,
        Quote $backendQuoteSession

    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_productloader = $_productloader;
        $this->_checkoutSession = $checkoutSession;
        $this->backendQuoteSession = $backendQuoteSession;
        parent::__construct($context);
    }
    public function isModuleEnabled()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $isEnabled = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_IS_ENABLED, $storeScope);
        return $isEnabled;
    }
    public function isQrEnabled()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $isQrEnabled = $this->scopeConfig->getValue(self::CONFIG_QR_ENABLED, $storeScope);
        return $isQrEnabled;
    }
    public function isGstApplyOnShpping()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $isShipping = $this->scopeConfig->getValue(self::CONFIG_GST_ONSHIPPING, $storeScope);
        return $isShipping;
    }

    public function getGstTaxperConfig()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $fee = $this->scopeConfig->getValue(self::CONFIG_GST_TAXPER, $storeScope);
        return $fee;
    }
    public function getGstTaxMinPriceConfig()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $fee = $this->scopeConfig->getValue(self::CONFIG_GST_MIN_PRICE, $storeScope);
        return $fee;
    }
    public function getGstTaxPerMinPriceConfig()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $fee = $this->scopeConfig->getValue(self::CONFIG_GST_TAXPER_MIN_PRICE, $storeScope);
        return $fee;
    }
    public function getGstNumber()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $gstnumber = $this->scopeConfig->getValue(self::CONFIG_GST_NUMBER, $storeScope);
        return $gstnumber;
    }
    public function getPanNumber()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $gstnumber = $this->scopeConfig->getValue(self::CONFIG_PAN_NUMBER, $storeScope);
        return $gstnumber;
    }
    public function getCinNumber()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $gstnumber = $this->scopeConfig->getValue(self::CONFIG_CIN_NUMBER, $storeScope);
        return $gstnumber;
    }
    public function getGstStateConfig()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $gststate = $this->scopeConfig->getValue(self::CONFIG_GST_STATE, $storeScope);
        return $gststate;
    }
    public function getGstTaxType()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $gststate = $this->scopeConfig->getValue(self::CONFIG_GST_TAXTYPE, $storeScope);
        return $gststate;
    }
    public function getShippingGstTaxType()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $gststate = $this->scopeConfig->getValue(self::CONFIG_GST_SHIPPING_TAXTYPE, $storeScope);
        return $gststate;
    }


    public function getAuthenticationSignature()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $signature = $this->scopeConfig->getValue(self::CONFIG_GST_SIGNATURE, $storeScope);
        return $signature;
    }
    public function getAuthenticationSignatureText()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $signaturetext = $this->scopeConfig->getValue(self::CONFIG_GST_SIGNATURETEXT, $storeScope);
        return $signaturetext;
    }
    public function getStateCode($address)
    {

        $CustomerRegionId=$address->getRegionId();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $region = $objectManager->create('Magento\Directory\Model\Region')
            ->load($CustomerRegionId);
        return $region->getStateCode();
    }
    public function getBuyerGst()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $buyerGst = $this->scopeConfig->getValue(self::CONFIG_GST_BUYERGST, $storeScope);
        return $buyerGst;
    }

    public function getCgstCharge($quote=null)
    {
        $taxPrice=0;
        try
        {
            if($this->_checkoutSession->getQuote()->isVirtual())
            {
                $shippingAddress = $this->_checkoutSession->getQuote()->getBillingAddress();
            }
            else {
                $shippingAddress = $this->_checkoutSession->getQuote()->getShippingAddress();
                if (!$shippingAddress->getCountryId()) {
                    $cart = $this->backendQuoteSession->getQuote();
                    if($cart->isVirtual()) {
                        $shippingAddress = $cart->getBillingAddress();
                    }
                    else {
                        $shippingAddress = $cart->getShippingAddress();
                    }
                }
            }
            if($shippingAddress)
            {
                $taxPrice= $this->calculateGst();
                $CountryId=$shippingAddress->getCountryId();
                $CustomerRegionId=$shippingAddress->getRegionId();
                $SystemRegionId=$this->getGstStateConfig();
                if(!($CountryId=='IN' && $CustomerRegionId==$SystemRegionId))
                {
                    $taxPrice=0;
                }
            }
        }
        catch(\Exception $e)
        {
        }
        return $taxPrice/2;
    }
    public function getSgstCharge($quote=null)
    {
        $taxPrice=0;
        try
        {
            if($this->_checkoutSession->getQuote()->isVirtual())
            {
                $shippingAddress = $this->_checkoutSession->getQuote()->getBillingAddress();
            }
            else {
                $shippingAddress = $this->_checkoutSession->getQuote()->getShippingAddress();
                if (!$shippingAddress->getCountryId()) {
                    $cart = $this->backendQuoteSession->getQuote();
                    if($cart->isVirtual()) {
                        $shippingAddress = $cart->getBillingAddress();
                    }
                    else {
                        $shippingAddress = $cart->getShippingAddress();
                    }
                }
            }
            if($shippingAddress)
            {
                $taxPrice=$this->calculateGst();
                $CountryId=$shippingAddress->getCountryId();
                $CustomerRegionId=$shippingAddress->getRegionId();
                $SystemRegionId=$this->getGstStateConfig();
                if(!($CountryId=='IN' && $CustomerRegionId==$SystemRegionId))
                {
                    $taxPrice=0;
                }
            }
        }
        catch(\Exception $e)
        {
        }
        return $taxPrice/2;
    }
    public function getIgstCharge($quote=null)
    {
        $taxPrice=0;
        try
        {
            if($this->_checkoutSession->getQuote()->isVirtual())
            {
                $shippingAddress = $this->_checkoutSession->getQuote()->getBillingAddress();
            }
            else {
                $shippingAddress = $this->_checkoutSession->getQuote()->getShippingAddress();
                if (!$shippingAddress->getCountryId()) {
                    $cart = $this->backendQuoteSession->getQuote();
                    if($cart->isVirtual()) {
                        $shippingAddress = $cart->getBillingAddress();
                    }
                    else {
                        $shippingAddress = $cart->getShippingAddress();
                    }
                }
            }
            if($shippingAddress)
            {
                $taxPrice=$this->calculateGst();
                $CountryId=$shippingAddress->getCountryId();
                $CustomerRegionId=$shippingAddress->getRegionId();
                $SystemRegionId=$this->getGstStateConfig();

                if($CountryId!='IN' || $CustomerRegionId==$SystemRegionId)
                {
                    $taxPrice=0;
                }
            }
        }
        catch(\Exception $e)
        {
        }
        return $taxPrice;
    }
    public function getMaxPercentage()
    {
        $cart = $this->_checkoutSession->getQuote();
        $gstPercent=0;
        foreach ($cart->getAllVisibleItems() as $item)
        {
            $product=$this->_productloader->create()->load($item->getProductId());
            $gstPercent=$product->getGstSource();
            $gstPercentMinPrice=$product->getGstSourceMinprice();
            $gstPercentAfterMinprice=$product->getGstSourceAfterMinprice();
            if($gstPercent<=0)
            {
                $cats = $product->getCategoryIds();
                foreach ($cats as $category_id)
                {
                    $_cat = $this->_categoryFactory->create()->load($category_id) ;
                    $gstPercent=$_cat->getGstCatSource();
                    $gstPercentMinPrice=$_cat->getGstCatSourceMinprice();
                    $gstPercentAfterMinprice=$_cat->getGstCatSourceAfterMinprice();
                    if($gstPercent!='')
                    {
                        if($gstPercentMinPrice > 0)
                        {
                            $gstPercent=$gstPercentAfterMinprice;
                        }
                        break;
                    }
                }
            }
            else
            {
                if($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice )
                {
                    $gstPercent=$gstPercentAfterMinprice;
                }
            }
        }
        return $gstPercent;
    }
    public function calculateGst()
    {

        try
        {
            if(!($this->isModuleEnabled()))
            {
                return 0;
            }

            $cart = $this->_checkoutSession->getQuote();

            if($this->_checkoutSession->getQuote()->isVirtual())
            {
                $shippingAddress = $this->_checkoutSession->getQuote()->getBillingAddress();
            }
            else {
                $shippingAddress =  $this->_checkoutSession->getQuote()->getShippingAddress();
                if (!$shippingAddress->getCountryId()) {
                    $cart = $this->backendQuoteSession->getQuote();
                    if($cart->isVirtual()) {
                        $shippingAddress = $cart->getBillingAddress();
                    }
                    else {
                        $shippingAddress = $cart->getShippingAddress();
                    }
                }
            }

            if($shippingAddress)
            {

                $CountryId=$shippingAddress->getCountryId();
                $CustomerRegionId=$shippingAddress->getRegionId();
                $SystemRegionId=$this->getGstStateConfig();
                if($CountryId!='IN')
                {
                    return 0;
                }
                $TotalGstPrice=0;

                if($this->getGstTaxType())
                {
                    $cart->setExclPrice(1);
                }
                else
                {
                    $cart->setExclPrice(0);
                }

                if($this->getShippingGstTaxType())
                {
                    $cart->setShipExclPrice(1);
                }
                else
                {
                    $cart->setShipExclPrice(0);
                }
                $cart->save();
                foreach ($cart->getAllVisibleItems() as $item)
                {
                    $gstPercent=0;
                    $product=$this->_productloader->create()->load($item->getProductId());
                    $itemPriceAfterDiscount= ($item->getPrice() * $item->getDiscountPercent())/100 ;
                    $prdPrice=$item->getPrice()-$itemPriceAfterDiscount;
                    $gstPercent=$product->getGstSource();
                    $gstPercentMinPrice=$product->getGstSourceMinprice();
                    $gstPercentAfterMinprice=$product->getGstSourceAfterMinprice();
                    if($gstPercent<=0)
                    {
                        $cats = $product->getCategoryIds();
                        foreach ($cats as $category_id)
                        {
                            $_cat = $this->_categoryFactory->create()->load($category_id) ;
                            $gstPercent=$_cat->getGstCatSource();
                            $gstPercentMinPrice=$_cat->getGstCatSourceMinprice();
                            $gstPercentAfterMinprice=$_cat->getGstCatSourceAfterMinprice();
                            if($gstPercent!='')
                            {
                                if($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice )
                                {
                                    $gstPercent=$gstPercentAfterMinprice;
                                }
                                break;
                            }
                        }
                    }
                    else
                    {
                        if($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice )
                        {
                            $gstPercent=$gstPercentAfterMinprice;
                        }
                    }
                    if($gstPercent<=0)
                    {
                        $gstPercent=$this->getGstTaxperConfig();
                        //$gstPercent				=	$this->getGstTaxperConfig();
                        $gstPercentMinPrice		=	$this->getGstTaxMinPriceConfig();
                        $gstPercentAfterMinprice	=	$this->getGstTaxPerMinPriceConfig();
                        if($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice )
                        {
                            $gstPercent=$gstPercentAfterMinprice;
                        }
                    }
                    $qty          = $item->getQty();
                    $rowTotal     = $item->getRowTotal();
                    $DiscountAmount=$item->getDiscountAmount();
                   /* if($this->getGstTaxType())
                    {
                        $GstPrice= ((($rowTotal-$DiscountAmount)*$gstPercent)/100);
                    }
                    else
                    {
                        $totalPercent = 100 + $gstPercent;
                        $perPrice     = ($rowTotal-$DiscountAmount) / $totalPercent;
                        $GstPrice     = $perPrice * $gstPercent;
                    }*/
                    $kerelaPer = 0;
		     if ($shippingAddress->getRegion() == 'Kerala') {
				$kerelaPer = 1;
		     }
                    if ($this->getGstTaxType()) {
                        $GstPrice = ((($rowTotal - $DiscountAmount) * $gstPercent) / 100);
                    } else {
                        $totalPercent = 100 + $gstPercent + $kerelaPer;
                        $perPrice = ($rowTotal - $DiscountAmount) / $totalPercent;
                        $GstPrice = $perPrice * $gstPercent;
                    }


                    $TotalGstPrice+=$GstPrice;
                    if($CountryId=='IN' && $CustomerRegionId==$SystemRegionId)
                    {
                        $item->setCgstCharge($GstPrice/2);
                        $item->setCgstPercent($gstPercent/2);
                        $item->setSgstCharge($GstPrice/2);
                        $item->setSgstPercent($gstPercent/2);
                    }
                    else if ($CountryId=='IN' && $CustomerRegionId!=$SystemRegionId)
                    {
                        $item->setIgstCharge($GstPrice);
                        $item->setIgstPercent($gstPercent);
                    }
                    if($this->getGstTaxType())
                    {
                        $item->setExclPrice(1);
                    }
                    else
                    {
                        $item->setExclPrice(0);
                    }

                    if($this->getShippingGstTaxType())
                    {
                        $cart->setShipExclPrice(1);
                    }
                    else
                    {
                        $cart->setShipExclPrice(0);
                    }
                    $item->save();

                }
                //$this->_checkoutSession->getQuote()->collectTotals()->save();
            }
        }
        catch(\Exception $e)
        {
        }
        return  $TotalGstPrice;
    }
    public function getShippingCgstCharge($quote=null)
    {
        try
        {
            $address =  $this->_checkoutSession->getQuote()->getShippingAddress();
            $quote =  $this->_checkoutSession->getQuote();
            if(!$address->getCountryId()) {
                $address = $this->backendQuoteSession->getQuote()->getShippingAddress();
                $quote =  $this->backendQuoteSession->getQuote();
            }

            if($address)
            {
                $countryId = $address->getCountryId();
                $customerRegionId = $address->getRegionId();
                $systemRegionId = $this->getGstStateConfig();
                $maxGstPercent = $gstPercent = 0;
                foreach ($quote->getAllVisibleItems() as $item)
                {

                    if($countryId == 'IN' && $customerRegionId==$systemRegionId)
                    {
                        $gstPercent = $item->getCgstPercent();

                    }
                    if ($gstPercent > $maxGstPercent)
                        $maxGstPercent = $gstPercent;
                }
                if($this->getShippingGstTaxType())
                {
                    $shippingGst = $address->getShippingAmount() * ($maxGstPercent/100);

                }else{
                    $shippingGstTotal = 100 + $maxGstPercent;
                    $shippingGstPeracent = $address->getShippingAmount() / $shippingGstTotal;
                    $shippingGst = $shippingGstPeracent * $maxGstPercent;
                }
                /*$address->setPercentShippingCgstCharge($maxGstPercent);
                $address->setShippingCgstCharge($shippingGst);*/
            }

        }
        catch(\Exception $e)
        {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
        }
        return $shippingGst;
    }

    public function getShippingSgstCharge($quote=null)
    {
        try
        {
            $address =  $this->_checkoutSession->getQuote()->getShippingAddress();
            $quote =  $this->_checkoutSession->getQuote();
            if(!$address->getCountryId()) {
                $address = $this->backendQuoteSession->getQuote()->getShippingAddress();
                $quote =  $this->backendQuoteSession->getQuote();
            }
            if($address)
            {
                $countryId = $address->getCountryId();
                $customerRegionId = $address->getRegionId();
                $systemRegionId = $this->getGstStateConfig();
                $maxGstPercent = $gstPercent = 0;
                foreach ($quote->getAllVisibleItems() as $item)
                {

                    if($countryId == 'IN' && $customerRegionId==$systemRegionId)
                    {
                        $gstPercent = $item->getCgstPercent();

                    }
                    if ($gstPercent > $maxGstPercent)
                        $maxGstPercent = $gstPercent;
                }
                if($this->getShippingGstTaxType())
                {
                    $shippingGst = $address->getShippingAmount() * ($maxGstPercent/100);

                }else{
                    $shippingGstTotal = 100 + $maxGstPercent;
                    $shippingGstPeracent = $address->getShippingAmount() / $shippingGstTotal;
                    $shippingGst = $shippingGstPeracent * $maxGstPercent;
                }
                /*$address->setPercentShippingCgstCharge($maxGstPercent);
                $address->setShippingCgstCharge($shippingGst);*/
            }

        }
        catch(\Exception $e)
        {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
        }
        return $shippingGst;
    }

    public function getShippingIgstCharge($quote=null)
    {
        try
        {
            $address =  $this->_checkoutSession->getQuote()->getShippingAddress();
            $quote =  $this->_checkoutSession->getQuote();
            if(!$address->getCountryId()) {
                $address = $this->backendQuoteSession->getQuote()->getShippingAddress();
                $quote =  $this->backendQuoteSession->getQuote();
            }
            if($address)
            {
                $countryId = $address->getCountryId();
                $customerRegionId = $address->getRegionId();
                $systemRegionId = $this->getGstStateConfig();

                $maxGstPercent = $gstPercent = 0;
                foreach ($quote->getAllVisibleItems() as $item)
                {

                    if($countryId == 'IN' && $customerRegionId!=$systemRegionId)
                    {
                        $gstPercent = $item->getIgstPercent();

                    }
                    if ($gstPercent > $maxGstPercent)
                        $maxGstPercent = $gstPercent;
                }

                if($this->getShippingGstTaxType()):
                    $shippingGst = $address->getShippingAmount() * ($maxGstPercent/100);

                else:
                    $shippingGstTotal = 100 + $maxGstPercent;
                    $shippingGstPeracent = $address->getShippingAmount() / $shippingGstTotal;
                    $shippingGst = $shippingGstPeracent * $maxGstPercent;
                endif;

                /*$address->setPercentShippingIgstCharge($maxGstPercent);
                $address->setShippingIgstCharge($shippingGst);*/
            }

        }
        catch(\Exception $e)
        {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
        }
        return $shippingGst;
    }
}
