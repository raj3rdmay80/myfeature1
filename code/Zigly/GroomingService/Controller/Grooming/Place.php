<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Controller\Grooming;

use Razorpay\Magento\Model\Config;
use Magento\Customer\Model\Customer;
use Zigly\GroomingService\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\View\Result\PageFactory;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Catalog\Model\Session as CatalogSession;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Razorpay\Magento\Controller\Payment\Order as Razor;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Zigly\GroomingService\Action\CreateServiceBookingAsOrder;

class Place extends \Magento\Framework\App\Action\Action
{

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Session
     */
    protected $groomingSession;

    /**
     * Customer session model
     *
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var CatalogSession
     */
    protected $catalogSession;

    /**
     * @var Razor
     */
    protected $razor;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var AddressFactory
     */
    protected $address;

    /** @var CreateServiceBookingAsOrder */
    protected $actionToCreateOrder;

    /**
     * Constructor
     *
     * @param Razor $razor
     * @param Config $config
     * @param Context $context
     * @param Customer $customer
     * @param AddressFactory $address
     * @param Session $groomingSession
     * @param GroomingFactory $grooming
     * @param JsonFactory $jsonResultFactory
     * @param CatalogSession $catalogSession
     * @param CustomerSession $customerSession
     * @param CustomerFactory $customerFactory
     * @param CreateServiceBookingAsOrder $actionToCreateOrder
     */
    public function __construct(
        Razor $razor,
        Config $config,
        Context $context,
        Customer $customer,
        AddressFactory $address,
        Session $groomingSession,
        GroomingFactory $grooming,
        CatalogSession $catalogSession,
        JsonFactory $jsonResultFactory,
        CustomerFactory $customerFactory,
        CustomerSession $customerSession,
        CreateServiceBookingAsOrder $actionToCreateOrder
    ) {
        $this->razor = $razor;
        $this->config = $config;
        $this->address = $address;
        $this->customer = $customer;
        $this->grooming = $grooming;
        $this->catalogSession = $catalogSession;
        $this->customerFactory = $customerFactory;
        $this->groomingSession = $groomingSession;
        $this->customerSession = $customerSession;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->actionToCreateOrder = $actionToCreateOrder;
        parent::__construct($context);
    }

    /**
     * Execute save action
     * @return void
     */
    public function execute()
    {
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/groomBook.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('-----------------------(Book...)--------------------------------');
        $serviceData = $this->getRequest()->getPostValue();
        $result = $this->jsonResultFactory->create();
        $responseData = [
            'success' => false,
            'message' => 'Something went wrong. Please reload and try again'
        ];
        $customerId = $this->customerSession->getCustomerId();
        $customer = $this->customerSession->getCustomer();

        $groomSession = $this->groomingSession->getGroomService();
        if (empty($serviceData['mode']) && $groomSession['grand_total'] > 0) {
            $responseData['message'] = 'Please select a payment.';
            $result->setData($responseData);
            return $result;
        }
        if ($serviceData['mode'] == 'pay-later' && !empty($groomSession['wallet_amount'])) {
            $responseData['success'] = false;
            $responseData['message'] = 'Illegal action performed.';
            $result->setData($responseData);
            return $result;
        }
        try {
            if (!empty($groomSession)) {
                $logger->info('-----------------------Groom Session exists-------------------------------');
                $logger->debug(var_export($groomSession, true));
                $bookGrooming = $this->grooming->create();
                $shippingAddress = $this->address->create()->load($groomSession['address_id']);
                $bookGrooming->setCustomerId($customerId)
                    ->setScheduledDate($groomSession['selected_date'])
                    ->setScheduledTime($groomSession['selected_time'])
                    ->setScheduledTimestamp($groomSession['selected_timestamp'])
                    ->setStreet($shippingAddress->getData('street'))
                    ->setRegion($shippingAddress->getRegion())
                    ->setRegionId($shippingAddress->getRegionId())
                    ->setCity($shippingAddress->getCity())
                    ->setPostcode($shippingAddress->getPostcode())
                    ->setPhoneNo($shippingAddress->getTelephone())
                    ->setPlanName($groomSession['plan_name'])
                    ->setProductSku($groomSession['plan_sku'])
                    ->setPlanActivites(json_encode($groomSession['activities']))
                    ->setSubtotal($groomSession['subtotal'])
                    ->setGrandTotal($groomSession['grand_total'])
                    ->setPetName($groomSession['pet_name'])
                    ->setPetCategory($groomSession['pet_category'])
                    ->setPetBreed($groomSession['pet_breed'])
                    ->setPetSpecies($groomSession['pet_species'])
                    ->setPetDob($groomSession['pet_dob'])
                    ->setPetAge($groomSession['pet_age'])
                    ->setPetGender($groomSession['pet_gender'])
                    ->setIsCustom($groomSession['is_custom'])
                    ->setBookingStatus('Scheduled')
                    ->setBookingType('1')
                    ->setCenter('At Home')
                    ->setPaymentStatus('Pending')
                    ->setPlanid($groomSession['planid'])
                    ->setWalletMoney($groomSession['wallet_amount'])
                    ->setPetId($groomSession['pet_id']);
                if (!empty($groomSession['coupon_code'])) {
                    $bookGrooming->setCouponCode($groomSession['coupon_code']);
                    $bookGrooming->setCouponAmount($groomSession['coupon_amount']);
                    $bookGrooming->setCouponDescription($groomSession['coupon_description']);
                }
                if ($serviceData['mode'] == 'pay-now') {
                    $bookGrooming->setPaymentMode('pay-now')->setVisibility(1)->save();
                    // $this->actionToCreateOrder->createIntoOrder($bookGrooming);
                } else if ($serviceData['mode'] == 'pay-later') { //&& !empty($groomSession['wallet_amount'])
                    $paymentMethod = 'services_cod';
                    $bookGrooming->setPaymentMode('pay-later')->setVisibility(2)->save();
                    $this->actionToCreateOrder->createIntoOrder($bookGrooming, $paymentMethod);
                } else if ($groomSession['grand_total'] == 0) {
                    $paymentMethod = 'services_cod';
                    $bookGrooming->setPaymentMode('pay-now')->setBookingStatus('Scheduled')->setPaymentStatus('Paid')->setVisibility(2)->save();
                    $this->actionToCreateOrder->createIntoOrder($bookGrooming, $paymentMethod);
                }
                $logger->info('-----------------------BooKeD-------------------------------');
                $responseData['success'] = true;
                $responseData['message'] = '';
                if ($serviceData['mode'] == 'pay-now') {
                    $razorpayOrderId = $this->razororderId($bookGrooming);
                    $logger->info('-----------------------Check-------------------------------');
                    $logger->info(print_r($razorpayOrderId, true));
                    $responseData['razorData']['razorId'] = $razorpayOrderId;
                    // $responseData['razorData']['orderId'] = $this->encryptBookingId($bookGrooming->getEntityId(), $customerId);
                    $responseData['razorData']['orderId'] = $bookGrooming->getEntityId();
                    $responseData['razorData']['amount'] = (int) (number_format($bookGrooming->getGrandTotal() * 100, 0, ".", ""));
                    $responseData['razorConfig']['key'] = $this->getKeyId();
                    $responseData['razorConfig']['name'] = $this->getMerchantNameOverride();
                    $responseData['razorConfig']['customerName'] = $customer->getFirstname();
                    $responseData['razorConfig']['customerEmail'] = $customer->getEmail();
                    $responseData['razorConfig']['customerPhoneNo'] = $customer->getPhoneNumber();
                    $groomSession['booking_razor_id'] = $responseData['razorData']['razorId'];
                    $bookGrooming->setRazorpayOrderId($razorpayOrderId)->save();
                }
                $groomSession['booking_id'] = $bookGrooming->getEntityId();
            }
            $logger->info('-----------------------PAYMODE-------------------------------');
            $logger->debug(var_export($serviceData['mode'], true));
        } catch (\Exception $e) {
            $logger->info('-----------------------Exception-------------------------------');
            $logger->debug(var_export($e->getMessage(), true));
            $responseData = [
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getMessage()
            ];
        }
        $this->groomingSession->setGroomService($groomSession);
        $result->setData($responseData);
        $logger->info('-----------------------SessionData-------------------------------');
        $logger->debug(var_export($groomSession, true));
        $logger->info('-----------------------BookingData-------------------------------');
        $logger->debug(var_export($bookGrooming->getData(), true));
        $logger->info('-----------------------ResponseData-------------------------------');
        $logger->debug(var_export($responseData, true));
        return $result;
    }

    /**
     * @return string|void
     */
    public function getMerchantNameOverride()
    {
        if (!$this->config->isActive()) {
            return '';
        }
        return $this->config->getMerchantNameOverride();
    }

    /**
     * @return string|void
     */
    public function encryptBookingId($bookingId, $customerId)
    {
        $ciphering = "AES-128-CTR";
        $iv_length = \openssl_cipher_iv_length($ciphering);
        $options = 0;
        $encryption_iv = '1234567891666222';
        $encryption_key = "inziglySG";
        $encryption = \openssl_encrypt($bookingId.':'.$customerId, $ciphering,
        $encryption_key, $options, $encryption_iv);
        // print_r($encryption);
        // exit();
        return $encryption;
    }

    /**
     * @return string|void
     */
    public function getKeyId()
    {
        if (!$this->config->isActive()) {
            return '';
        }
        return $this->config->getKeyId();
    }

    /**
     * create razor order
     *
     * @param mixed $orderId Quote id.
     * @return mixed
     */
    public function razororderId($service)
    {
        
        $payment_action = $this->razor->config->getPaymentAction();
        if ($payment_action === 'authorize') {
            $payment_capture = 0;
        } else {
            $payment_capture = 1;
        }

        $responseContent = [
                'success'   => false,
                'message'   => 'Unable to create your order. Please contact support.',
                'parameters' => []
            ];
        try {
            $amount = (int) (number_format($service->getGrandTotal() * 100, 0, ".", ""));
            $order = $this->razor->rzp->order->create([
                'amount' => $amount,
                'receipt' => $service->getEntityId(),
                'currency' => 'INR',
                'payment_capture' => $payment_capture,
                'app_offer' => ($this->razor->getDiscount() > 0) ? 1 : 0
            ]);

            $responseContent = [
                'success'   => false,
                'message'   => 'Unable to create your order. Please contact support.',
                'parameters' => []
            ];

            if (null !== $order && !empty($order->id))
            {
                $responseContent = [
                    'success'           => true,
                    'rzp_order'         => $order->id,
                ];
                return $order->id;
            }
        } catch(\Razorpay\Api\Errors\Error $e) {
            $responseContent = [
                'success'   => false,
                'message'   => $e->getMessage(),
                'parameters' => []
            ];
        } catch(\Exception $e) {
            $responseContent = [
                'success'   => false,
                'message'   => $e->getMessage(),
                'parameters' => []
            ];
        }
        return $responseContent;

    }
}