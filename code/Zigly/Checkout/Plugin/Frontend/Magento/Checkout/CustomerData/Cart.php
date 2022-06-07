<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Checkout
 */
declare(strict_types=1);

namespace Zigly\Checkout\Plugin\Frontend\Magento\Checkout\CustomerData;

use Magento\Checkout\CustomerData\Cart as CartData;
use Magento\Checkout\Model\Session;
use Magento\Checkout\Helper\Data;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Cart
{
    const XML_PATH_FREE_SHIPPING = 'zigly_general/checkout/free_shipping';

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var Data
     */
    protected $checkoutHelper;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param Session $checkoutSession
     * @param SerializerInterface $serializer
     * @param ScopeConfigInterface $scopeConfig
     * @param Data $checkoutHelper
     */
    public function __construct(
        Session $checkoutSession,
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        Data $checkoutHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
        $this->checkoutHelper = $checkoutHelper;
    }

    /**
     * Plug in with cart customer section data
     * 
     * @param CartData $quote
     * @param CartData $result
     * @return CartData
     */
    public function afterGetSectionData(
        CartData $subject,
        $result
    ) {
        //Your plugin code
        $totals = $this->getQuote()->getTotals();
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');

        /*$cartTotalRepository = $objectManager->create('Magento\Quote\Model\Cart\CartTotalRepository');
        var_dump($this->getQuote()->getId());

        $totals2 = $cartTotalRepository->get($this->getQuote()->getId());

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/testCART.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Your text message');
        $discountAmount = $totals2->getDiscountAmount();

        $discountTotal = 0;
        foreach ($this->getQuote()->getAllItems() as $item){
        $discountTotal += $item->getDiscountAmount();
        }
        $logger->info(print_r($discountTotal, true));
        $logger->info(print_r($discountAmount, true));*/

        $freeshippingAmount = $this->scopeConfig->getValue(
            self::XML_PATH_FREE_SHIPPING,
            ScopeInterface::SCOPE_STORE
        );
        // var_dump(number_format((float)$result['subtotalAmount'], 0, '.', ''));
        if (number_format((float)$result['subtotalAmount'], 0, '.', '') >= (int)$freeshippingAmount) {
            $addFreeShipping = null;
        } else {
            $addFreeShipping = $this->checkoutHelper->formatPrice($freeshippingAmount - number_format((float)$result['subtotalAmount'], 0, '.', ''));
        }
        /*var_dump($freeshippingAmount);
        echo "<pre>";
        echo "---";
        // var_dump($totals["discount"]->getValue());
        var_dump($discountTotal);
        // print_r($this->getQuote()->getShippingAddress()->getShippingAmount());
        echo "-SHip--";
        print_r($totals['tax']->getValue());
        echo "--Tax-";
        print_r($totals['grand_total']->getValue());


        echo "</pre>";*/
        $grandTotal = $totals['grand_total']->getValue();
        $shippingAmount = $this->getQuote()->getShippingAddress()->getShippingAmount();
        $result['zg_ship_amount'] = !empty((int)$shippingAmount)
                ? $this->checkoutHelper->formatPrice($shippingAmount)
                : 0;
        $result['zg_free_ship_amount'] = !empty((int)$freeshippingAmount)
                ? $this->checkoutHelper->formatPrice($freeshippingAmount)
                : 0;
        $result['zg_total'] = !empty($grandTotal)
                ? $this->checkoutHelper->formatPrice($grandTotal)
                : 0;
        $result['zg_total_tax_amount'] = "₹00";
        $result['zg_for_freeship_amount_add'] = $addFreeShipping;
        $result['zg_saved_amount'] = "₹00";
        $result['zg_wallet_used'] = '';
        if (!empty($this->getQuote()->getZwallet())) {
            $zwallet = $this->serializer->unserialize($this->getQuote()->getZwallet());
            if ($zwallet['applied'] == true && !empty($zwallet['spend_amount'])) {
                $result['zg_wallet_used'] = $this->checkoutHelper->formatPrice($zwallet['spend_amount']);
            }
        }
        return $result;
    }

    /**
     * Get active quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    protected function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }
}

