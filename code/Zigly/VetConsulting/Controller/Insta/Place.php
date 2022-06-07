<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Controller\Insta;

use Razorpay\Magento\Model\Config;
use Magento\Customer\Model\Customer;
use Zigly\VetConsulting\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\View\Result\PageFactory;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Catalog\Model\Session as CatalogSession;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Razorpay\Magento\Controller\Payment\Order as Razor;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Zigly\GroomingService\Action\CreateServiceBookingAsOrder;
use Zigly\ScheduleManagement\Model\ScheduleManagementFactory;
use Zigly\ScheduleManagement\Model\ResourceModel\ScheduleManagement\CollectionFactory;

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
    protected $vetSession;

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
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * Constructor
     *
     * @param Razor $razor
     * @param Config $config
     * @param Context $context
     * @param Customer $customer
     * @param AddressFactory $address
     * @param Session $vetSession
     * @param GroomingFactory $grooming
     * @param JsonFactory $jsonResultFactory
     * @param CatalogSession $catalogSession
     * @param CustomerSession $customerSession
     * @param CustomerFactory $customerFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $collectionFactory
     * @param CreateServiceBookingAsOrder $actionToCreateOrder
     * @param ScheduleManagementFactory $scheduleManagementModelFactory
     */
    public function __construct(
        Razor $razor,
        Config $config,
        Context $context,
        Customer $customer,
        Session $vetSession,
        AddressFactory $address,
        GroomingFactory $grooming,
        CatalogSession $catalogSession,
        JsonFactory $jsonResultFactory,
        CustomerFactory $customerFactory,
        CustomerSession $customerSession,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionFactory,
        CreateServiceBookingAsOrder $actionToCreateOrder,
        ScheduleManagementFactory $scheduleManagementModelFactory
    ) {
        $this->razor = $razor;
        $this->config = $config;
        $this->address = $address;
        $this->customer = $customer;
        $this->grooming = $grooming;
        $this->vetSession = $vetSession;
        $this->scopeConfig = $scopeConfig;
        $this->catalogSession = $catalogSession;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->collectionFactory = $collectionFactory;
        $this->actionToCreateOrder = $actionToCreateOrder;
        $this->scheduleManagementModelFactory = $scheduleManagementModelFactory;
        parent::__construct($context);
    }

    /**
     * Execute save action
     * @return void
     */
    public function execute()
    {
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/vetBook.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('-----------------------(Book...)--------------------------------');
        $vetData = $this->getRequest()->getPostValue();
        $result = $this->jsonResultFactory->create();
        $responseData = [
            'success' => false,
            'message' => 'Something went wrong. Please reload and try again'
        ];
        $customerId = $this->customerSession->getCustomerId();
        $customer = $this->customerSession->getCustomer();

        $consultSession = $this->vetSession->getVet();
        if (empty($vetData['mode']) && $consultSession['grand_total'] >= 0) {
            $responseData['message'] = 'Please select a payment.';
            $result->setData($responseData);
            return $result;
        }
        if ($vetData['mode'] == 'pay-now') {
            $status = 'Pending';
        } else {
            $status = 'Paid';
        }
        try {
            if (!empty($consultSession)) {
                $logger->info('-----------------------Vet Session exists-------------------------------');
                $logger->debug(var_export($consultSession, true));
                $bookVet = $this->grooming->create();
                /*$shippingAddress = $this->address->create()->load($consultSession['address_id']);*/
                $sku = $this->scopeConfig->getValue('vetconsulting/product_config/sku_for_insta', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $bookVet->setCustomerId($customerId)
                    ->setStreet('Insta Consult')
                    ->setRegion('Delhi')
                    ->setRegionId('542')
                    ->setCity('NA')
                    ->setPostcode('NA')
                    ->setPhoneNo($customer->getPhoneNumber())
                    ->setProductSku($sku)
                    ->setSubtotal($consultSession['subtotal'])
                    ->setGrandTotal($consultSession['grand_total'])
                    ->setBookingStatus('Scheduled')
                    ->setBookingType('2')
                    ->setCenter('Insta Consult')
                    ->setPaymentStatus($status)
                    ->setGroomerId($consultSession['vet_id'])
                    ->setWalletMoney($consultSession['wallet_amount']);
                $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                if ($now->format('i') > 30) {
                    $now->add(new \DateInterval('PT1H'));
                    $time = $now->format('h'). ':00'. $now->format('a');
                } else {
                    $time = $now->format('h'). ':30'. $now->format('a');
                }
                $bookVet->setScheduledDate($now->format('Y-m-d'));
                $bookVet->setScheduledTime($time);
                $asiaDateTime = new \DateTime($now->format('Y-m-d')." ".$time, new \DateTimeZone('Asia/Kolkata'));
                $currentTimeStamp = $asiaDateTime->getTimestamp() + $asiaDateTime->getOffset();
                $bookVet->setScheduledTimestamp($currentTimeStamp);
                $gmtTimezone = new \DateTimeZone('GMT');
                $gmtDateTime = new \DateTime($now->format('Y-m-d')." ".$time, $gmtTimezone);
                $scheduleCollection = $this->collectionFactory->create()->addFieldToFilter('booking_id', '0')->addFieldToFilter('professional_id', ['eq' => $consultSession['vet_id']])->addFieldToFilter('availability', '1')->addFieldToFilter('slot_start_time', ['eq' => $gmtDateTime]);
                if (!count($scheduleCollection->getData())){ 
                    $responseData['message'] = 'There is no slot availabile for this vet.';
                    $result->setData($responseData);
                    return $result;
                }
                if (!empty($consultSession['pain_points'])) {
                    $bookVet->setPainPoints(json_encode($consultSession['pain_points']));
                }
                if (!empty($consultSession['pain_description'])) {
                    $bookVet->setPainDescription($consultSession['pain_description']);
                }
                if (!empty($consultSession['image_document'])) {
                    $bookVet->setImageDocument($consultSession['image_document']);
                }
                if (!empty($consultSession['coupon_code'])) {
                    $bookVet->setCouponCode($consultSession['coupon_code']);
                    $bookVet->setCouponAmount($consultSession['coupon_amount']);
                    $bookVet->setCouponDescription($consultSession['coupon_description']);
                }
                if ($vetData['mode'] == 'pay-now') {
                    $bookVet->setPaymentMode('pay-now')->setVisibility(1)->save();
                } else {
                    $bookVet->setPaymentMode('pay-now')->setVisibility(2)->save();
                }
                if (count($scheduleCollection->getData())) {
                    $scheduleModel = $this->scheduleManagementModelFactory->create();
                    $scheduleModel->load($scheduleCollection->getData()[0]['schedulemanagement_id']);
                    $scheduleModel->setBookingId($bookVet->getEntityId());
                    $scheduleModel->save();
                }
                $logger->info('-----------------------BooKeD-------------------------------');
                $responseData['success'] = true;
                $responseData['message'] = '';
                if ($vetData['mode'] == 'pay-now') {
                    $razorpayOrderId = $this->razororderId($bookVet);
                    $responseData['razorData']['razorId'] = $razorpayOrderId;
                    $responseData['razorData']['orderId'] = $bookVet->getEntityId();
                    $responseData['razorData']['amount'] = (int) (number_format($bookVet->getGrandTotal() * 100, 0, ".", ""));
                    $responseData['razorConfig']['key'] = $this->getKeyId();
                    $responseData['razorConfig']['name'] = $this->getMerchantNameOverride();
                    $responseData['razorConfig']['customerName'] = $customer->getFirstname();
                    $responseData['razorConfig']['customerEmail'] = $customer->getEmail();
                    $responseData['razorConfig']['customerPhoneNo'] = $customer->getPhoneNumber();
                    $consultSession['booking_razor_id'] = $responseData['razorData']['razorId'];
                    $bookVet->setRazorpayOrderId($razorpayOrderId)->save();
                }
                $consultSession['booking_id'] = $bookVet->getEntityId();
            }
            $logger->info('-----------------------PAYMODE-------------------------------');
            /*$logger->debug(var_export($vetData['mode'], true));*/
        } catch (\Exception $e) {
            $logger->info('-----------------------Exception-------------------------------');
            $logger->debug(var_export($e->getMessage(), true));
            $responseData = [
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getMessage()
            ];
        }
        $this->vetSession->setVet($consultSession);
        $result->setData($responseData);
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
				$order = new \Magento\Framework\DataObject(array('groomer_id' => $order->id,'grooming_data'=>$order));
				$this->_eventManager->dispatch('zigly_videointegrate_display_order', ['groomer_data' => $order]);
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
