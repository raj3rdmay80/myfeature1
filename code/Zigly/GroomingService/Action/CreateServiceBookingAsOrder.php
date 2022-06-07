<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Action;

use Magento\Framework\App\Action\Context;
use Razorpay\Api\Api;
use Razorpay\Magento\Model\Config;
use Zigly\GroomingService\Model\Session;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Zigly\CouponService\Model\ResourceModel\CouponService\CollectionFactory as CouponCollection;

/**
 * Create Service Booking As Order
 */
class CreateServiceBookingAsOrder
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Session
     */
    protected $groomingSession;

    /** @var CustomerSession */
    protected $customerSession;

    /**
     * @var GroomingFactory
     */
    protected $groomingFactory;

    /** @var CartRepositoryInterface */
    protected $quoteRepository;

    /** @var QuoteFactory */
    protected $quoteFactory;

    /** @var ProductFactory */
    protected $productFactory;
    /**
     * @var CouponCollection
     */
    protected $couponCollection;

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

     /**
     * @param CartRepositoryInterface $quoteRepository
     * @param QuoteFactory $quoteFactory
     * @param Context     $context
     * @param ScopeConfigInterface $scopeConfig
     * @param GroomingFactory $GroomingFactory
     * @param ProductFactory $productFactory
     * @param CustomerSession $customerSession
     * @param Session $groomingSession
     * @param SerializerInterface $serializer
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        QuoteFactory $quoteFactory,
        Session $groomingSession,
        CustomerSession $customerSession,
        ProductFactory $productFactory,
        GroomingFactory $groomingFactory,
        CouponCollection $couponCollection
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
        $this->serializer = $serializer;
        $this->productFactory = $productFactory;
        $this->groomingSession = $groomingSession;
        $this->customerSession = $customerSession;
    	$this->grooming = $groomingFactory;
        $this->couponCollection = $couponCollection;
    }

    public function createIntoOrder($bookingData, $paymentMethod = 'services_razorpay')
    {
        try {
            $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/groomBookIntoOrder.log');
            $logger = new \Laminas\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('-----------------------(Booking ===> Order)--------------------------------');

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
            $productRepository = $objectManager->get('\Magento\Catalog\Api\ProductRepositoryInterface');
            $quoteManagement = $objectManager->get('Magento\Quote\Model\QuoteManagement');
            $currentQuote = $cart->getQuote();
            $quoteID = $currentQuote->getId();
            $logger->debug(var_export('BOOKingID---'.$bookingData->getEntityId(), true));
            $currentQuoteId = $this->quoteFactory->create()->getId();
            $customerId = $this->customerSession->getCustomerId();
            $customer = $this->customerSession->getCustomer();

            $storeId = $currentQuote->getStoreId();

            $virtualQuote = $this->quoteFactory->create();
            $virtualQuote->setCustomerId($customerId);
            $virtualQuote->setCustomerEmail($customer->getEmail());
            $virtualQuote->setCustomerIsGuest(false);
            if ($bookingData->getBookingType() == 1) {
                $virtualQuote->setOrderType('2');
            } elseif ($bookingData->getBookingType() == 2) {
                $virtualQuote->setOrderType('3');
            }
            $this->quoteRepository->save($virtualQuote);
            $logger->debug(var_export($virtualQuote->getId(), true));
            $logger->debug(var_export("currentQuoteId---".$quoteID, true));

            $virtualQuote = $this->quoteRepository->getActive($virtualQuote->getId());
            $sku = $bookingData->getProductSku();
            $productObject = $productRepository->get($sku);
                $logger->debug(var_export("skubookingData->getSesent---".$sku, true));
            if ($productObject) {
                $logger->debug(var_export("productObject Exists---".$productObject->getEntityId(), true));

                // $productObject->setPrice($bookingData->getSubtotal());
                // $productObject->setBasePrice($bookingData->getSubtotal());
                $virtualQuote->addProduct(
                    $productObject,
                    intval(1)
                );
                $productItem = $virtualQuote->getItemByProduct($productObject);;
                $productItem->setCustomPrice($bookingData->getSubtotal());
                $productItem->setOriginalCustomPrice($bookingData->getSubtotal());
                $productItem->setPrice($bookingData->getSubtotal());
                $productItem->setBasePrice($bookingData->getSubtotal());
                $productItem->setRowTotal($bookingData->getSubtotal());
                $productItem->setBaseRowTotal($bookingData->getSubtotal());
                $productItem->setDiscountAmount($bookingData->getSubtotal());
                $productItem->setBaseDiscountAmount($bookingData->getSubtotal());
                $productItem->getProduct()->setIsSuperMode(true);

                $shippingAddress = [
                    'firstname'    => $customer->getFirstname(), //address Details
                    'lastname'     => ($customer->getLastname()) ? $customer->getLastname() : '',
                    'street' => $bookingData->getStreet(),
                    'city' => $bookingData->getCity(),
                    'country_id' => 'IN',
                    'region' => $bookingData->getRegion(),
                    'region_id' => $bookingData->getRegionId(),
                    'postcode' => $bookingData->getPostcode(),
                    'telephone' => $bookingData->getPhoneNo(),
                    'save_in_address_book' => 0
                ];
                $logger->debug(var_export($shippingAddress, true));
                $virtualQuote->setCurrency();

                $virtualQuote->getBillingAddress()->addData($shippingAddress);
                $virtualQuote->getShippingAddress()->addData($shippingAddress);
                $virtualQuote->getShippingAddress()->setCollectShippingRates(true)->collectShippingRates()->setShippingMethod('flatrate_flatrate'); //shipping method
                $this->quoteRepository->save($virtualQuote);
                $virtualQuote->setPaymentMethod($paymentMethod);
                $virtualQuote->setInventoryProcessed(false);
                $virtualQuote->getPayment()->importData(['method' => $paymentMethod]);
                $logger->debug(var_export('----quote payment set---', true));
                $virtualQuote->setCustomerFirstname($customer->getFirstname());
                $virtualQuote->setCustomerLastname($customer->getLastname());
                $virtualQuote->reserveOrderId();
                $incrementId = $virtualQuote->getReservedOrderId();
                if ($bookingData->getBookingType() == 1) {
                    $newincrementId = 'SG'.$incrementId;
                } elseif ($bookingData->getBookingType() == 2) {
                    $newincrementId = 'VC'.$incrementId;
                }
                $virtualQuote->setReservedOrderId($newincrementId);
                // $virtualQuote->setGrandTotal($bookingData->getGrandTotal());
                // $virtualQuote->setBaseGrandTotal($bookingData->getGrandTotal());
                $virtualQuote->setInventoryProcessed(false);
                $virtualQuote->setTotalsCollectedFlag(false)->collectTotals();
                foreach ($virtualQuote->getAllAddresses() as $address) {
                    $address->setBaseSubtotal($bookingData->getSubtotal());
                    $address->setSubtotal($bookingData->getSubtotal());
                    $address->setDiscountAmount(0);
                    $address->setShippingAmount(0);
                    $address->setBaseShippingAmount(0);
                    $address->setShippingInclTax(0);
                    $address->setBaseShippingInclTax(0);
                    $address->setTaxAmount(0);
                    $address->setBaseTaxAmount(0);
                    $address->setBaseGrandTotal($bookingData->getGrandTotal());
                    $address->setGrandTotal($bookingData->getGrandTotal());
                }
                $zWallet = [];
                $logger->debug(var_export($bookingData->getWalletMoney(), true));
                if (!empty($bookingData->getWalletMoney())){
                    $zWallet['applied'] = true;
                    $zWallet['spend_amount'] = $bookingData->getWalletMoney();
                    $zWallet['is_service'] = true;
                    $virtualQuote->setZwallet($this->serializer->serialize($zWallet));
                    $logger->debug(var_export($virtualQuote->getZwallet(), true));
                }
                $this->quoteRepository->save($virtualQuote);
                $logger->debug(var_export('----quote saved---', true));
                $order = $quoteManagement->submit($virtualQuote);
                $logger->debug(var_export('----quote Submited---', true));
                //Check and get discount coupon
                if(!empty($bookingData->getCouponCode())){
                    $logger->debug(var_export('----coupon code start---', true));
                    $logger->debug(var_export($bookingData->getCouponCode(), true));
                    $couponData = $this->couponCollection->create()
                        ->addFieldToFilter('coupon_code', $bookingData->getCouponCode())
                        ->addFieldToFilter('status', 1)->getData();
        		    if (count($couponData)) {
                		$logger->debug(var_export('----count in---', true));
            			$discountAmount = -$couponData[0]['amount'];
                        $logger->debug(var_export('discountAmount : '.$discountAmount, true));
            			//$totalAfterDiscount = $bookingData->getGrandTotal() - $couponData[0]['amount'];
            			$totalAfterDiscount = $bookingData->getGrandTotal();	       
                        $logger->debug(var_export('totalAfterDiscount : '.$totalAfterDiscount, true));
            			$order->setCouponCode($bookingData->getCouponCode());
                        /*$order->setDiscountDescription($bookingData->getCouponDescription());*/
            			$order->setDiscountDescription($bookingData->getCouponCode());
                        $order->setBaseDiscountAmount($discountAmount);
                        $order->setBaseDiscountInvoiced($discountAmount);
                        $order->setDiscountAmount($discountAmount);
            			$order->setDiscountInvoiced($discountAmount);
            			$order->setBaseGrandTotal($totalAfterDiscount);
                        $order->setGrandTotal($totalAfterDiscount);
                        if ($paymentMethod == 'services_razorpay') {
                			$order->setTotalPaid($totalAfterDiscount);
                        }
                    }
                }
                $commissionPercentage = '';
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                if ($bookingData->getCenter() == "At Home"){
                    $commissionPercentage = $this->scopeConfig->getValue('commission/groomer/at_home', $storeScope);
                } elseif ($bookingData->getCenter() == "At Experience Center"){
                    $commissionPercentage = $this->scopeConfig->getValue('commission/groomer/at_center', $storeScope);
                }
                if (!empty($commissionPercentage)){
                    $professionalAmount = ($commissionPercentage / 100) * $bookingData->getSubtotal();
                    $bookingData->setProfessionalAmount($professionalAmount);
                    $bookingData->setCommissionPercentage($commissionPercentage);
                    $bookingData->save();
                }
                $order->setEmailSent(0);
                if ($paymentMethod == 'services_cod') {
                    $payment = $order->getPayment();
                    $payment->setMethod('cashondelivery');
                    $payment->save();
                }
                $order->setBookingId($bookingData->getEntityId());
                $shippingAmount = $order->getShippingAmount();

                $logger->debug(var_export('Shipping amount---'.$shippingAmount, true));
                /*$order->setOrderType('2')->save();*/
                $order->save();
                $logger->debug(var_export('ORDER ID---'.$order->getEntityId().'--'.$order->getRealOrderId(), true));
                $logger->debug(var_export('------------------------------------------------------', true));
            } else {
                $logger->debug(var_export("-----NO sku's product Present---", true));
            }
         } catch (\Exception $e) {
            print_r($e->getMessage());
            $logger->info('-----------------------Exception-------------------------------');
            $logger->debug(var_export($e->getMessage(), true));
        }
    }
}
