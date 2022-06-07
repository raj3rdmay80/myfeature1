<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Model\Quote\Collector;

// use Amasty\Rewards\Api\Data\SalesQuote\EntityInterface;
// use Amasty\Rewards\Model\Config\Source\RedemptionLimitTypes;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\SalesRule\Model\Validator;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Customer\Model\SessionFactory as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\Wallet\Helper\Data as WalletHelper;

class WalletAmount extends AbstractTotal
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var WalletHelper
     */
    private $walletHelper;

    public function __construct(
        Validator $validator,
        CustomerSession $customerSession,
        SerializerInterface $serializer,
        WalletHelper $walletHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->validator = $validator;
        $this->customerSession = $customerSession;
        $this->serializer = $serializer;
        $this->walletHelper = $walletHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ): self {
        $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/WalletCollector-'.$now->format('d-m-Y').'.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('------------------Wallet Collect START-------------------------');
        $logger->debug(var_export($this->walletHelper->isEnabled(), true));

        if (!$this->walletHelper->isEnabled()) { // || !$quote->getShippingAddress()->getShippingMethod()
            return $this;
        }
        /** @var $address Address */
        $address = $shippingAssignment->getShipping()->getAddress();
        $items = $shippingAssignment->getItems();
        /*if ($quote->getItemsCount() == 0) {
            $logger->info('----------------Cleared--------');
            $zwallet['applied'] = false;
            $zwallet['spend_amount'] = 0;
            $total->addTotalAmount('z_wallet', 0);
            $total->addBaseTotalAmount('z_wallet', 0);
            $quote->setZwallet($this->serializer->serialize($zwallet));
            return $this;
        }*/
        if (!$items || empty($quote->getZwallet())) { 
            /*$logger->info('----------------Cleared--------');
            $zwallet['applied'] = false;
            $zwallet['spend_amount'] = 0;
            $total->addTotalAmount('z_wallet', 0);
            $total->addBaseTotalAmount('z_wallet', 0);
            $quote->setZwallet($this->serializer->serialize($zwallet));*/
            return $this;
        }
        $zwallet = $this->serializer->unserialize($quote->getZwallet());
        if (isset($zwallet['is_service']) && $zwallet['is_service'] == true) {
            return $this;
        }


        $customer = $this->customerSession->create()->getCustomer();

        $logger->info('------------------Customer ID--------'.$customer->getId());
        $logger->info('------------------Quote ID-----------'.$quote->getEntityId());

        $logger->debug(var_export($zwallet, true));
        try {
            if ($zwallet['applied'] == false) { // empty($zwallet['spend_amount']) &&
                $logger->info('------------------applied False-------------------------');
                $zwallet['spend_amount'] = 0;
                $total->addTotalAmount('z_wallet', 0);
                $total->addBaseTotalAmount('z_wallet', 0);
            } 
            else if ($zwallet['applied'] == true) {
                $totalWalletBalance = is_null($customer->getWalletBalance()) ? 0 : (int)$customer->getWalletBalance();
                $logger->debug(var_export('--------Customer---balance-------'.$totalWalletBalance, true));
                $grandTotal = (float)$quote->getGrandTotal();
                $logger->debug(var_export('Initial subtotal'.$quote->getSubtotal(), true));
                $logger->debug(var_export('Initial subtotal'.$quote->getBaseGrandTotal(), true));
                $logger->debug(var_export('Initial GTotal'.$grandTotal, true));
                /*if ($grandTotal <= 0 && $quote->getItemsCount() == 1 && (int)$quote->getSubtotal() <= 0) {
                    $items = $quote->getAllItems();
                    foreach ($items as $item){
                        $grandTotal = (float)$item->getRowTotal();
                        $logger->debug(var_export('Initial item row total GRAND Total'.$item->getRowTotal(), true));
                    }
                }*/


                if (!empty($zwallet['spend_amount'])) {
                    $grandTotal += (float)$zwallet['spend_amount'];
                }
                $logger->debug(var_export('calculated GTotal'.$grandTotal, true));

                $maxTransactionPercent = (!empty($this->walletHelper->getMaxTransactionPercent())) ? (float)$this->walletHelper->getMaxTransactionPercent() : 0;
                $logger->debug(var_export((float)$totalWalletBalance <= $grandTotal, true));
                $logger->debug(var_export((float)$totalWalletBalance > $grandTotal, true));
                $logger->debug(var_export('Percent-------'.$maxTransactionPercent, true));
                $calculatedWalletApplicable = ($maxTransactionPercent / 100) * $grandTotal;
                $logger->debug(var_export('calculatedWalletApplicable-------'.$calculatedWalletApplicable, true));
                if ($calculatedWalletApplicable <= 0 || (float)$totalWalletBalance < 1) {
                    $zwallet['applied'] = false;
                    $zwallet['spend_amount'] = 0;
                    $total->addTotalAmount('z_wallet', 0);
                    $total->addBaseTotalAmount('z_wallet', 0);
                } else {
                    if ((float)$totalWalletBalance <= $calculatedWalletApplicable) {
                        $logger->info('---if-lessthanTotal--');
                        $zwallet['spend_amount'] = (int)$totalWalletBalance;
                    } else if ((float)$totalWalletBalance > $calculatedWalletApplicable) {
                        $logger->info('---elseif-greaterthanTotal---');
                        $zwallet['spend_amount'] = (int)$calculatedWalletApplicable;
                    }
                    $zwallet['percent'] = $maxTransactionPercent;
                    $total->addTotalAmount('z_wallet', -$zwallet['spend_amount']);
                    $total->addBaseTotalAmount('z_wallet', -$zwallet['spend_amount']);
                }
                $logger->info('------------------Applied True-------------------------'.$zwallet['spend_amount']);
            }
            $logger->debug(var_export($zwallet, true));
            $quote->setZwallet($this->serializer->serialize($zwallet));
            $logger->info('------------------END-------------------------');
        } catch (\Exception $e) {
            $logger->info('------------------END---CATCHED----------------------'.$e->getMessage());
        }

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    { 
        if (!$this->walletHelper->isEnabled() || empty($quote->getZwallet())) { //!$quote->getShippingAddress()->getShippingMethod()
            return [];
        }
        $zwallet = $this->serializer->unserialize($quote->getZwallet());

        if ($zwallet['applied'] == false || empty($zwallet['spend_amount'])) {
           return [];
        }
        return [
                'code' => 'zwallet',
                'title' => __('Wallet Used'),
                'value' => $zwallet['spend_amount'] 
            ];
    }

        /**
     * Clear tax related total values in address
     *
     * @param Address\Total $total
     * @return void
     */
    protected function clearValues(Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('shipping', 0);
        $total->setBaseTotalAmount('shipping', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
        $total->setShippingInclTax(0);
        $total->setBaseShippingInclTax(0);
        $total->setShippingTaxAmount(0);
        $total->setBaseShippingTaxAmount(0);
        $total->setShippingAmountForDiscount(0);
        $total->setBaseShippingAmountForDiscount(0);
        $total->setBaseShippingAmountForDiscount(0);
        $total->setTotalAmount('z_wallet', 0);
        $total->setBaseTotalAmount('z_wallet', 0);
    }
}
