<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Controller\Vet;

use Razorpay\Api\Api;
use Razorpay\Magento\Model\Config;
use Magento\Customer\Model\Customer;
use Zigly\VetConsulting\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Framework\App\Request\InvalidRequestException;
use Zigly\GroomingService\Action\CreateServiceBookingAsOrder;

/**
 * Razor pay callback booking Controller
 */
class Paynow extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{

    /** 
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request 
    ): ?InvalidRequestException {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Session
     */
    protected $vetSession;

    /**
     * @var GroomingFactory
     */
    protected $groomingFactory;

    /**
     * @var CreateServiceBookingAsOrder
     */
    protected $actionToCreateOrder;

    /**
     * Customer session model
     *
     * @var CustomerSession
     */
    protected $customerSession;

     /**
     * @param Config $config
     * @param Context $context
     * @param Customer $customer
     * @param Session $vetSession
     * @param GroomingFactory $GroomingFactory
     * @param CustomerSession $customerSession
     * @param CustomerFactory $customerFactory
     * @param CreateServiceBookingAsOrder $actionToCreateOrder
     */
    public function __construct(
        Context $context,
        Config $config,
        Session $vetSession,
        Customer $customer,
        CustomerFactory $customerFactory,
        CustomerSession $customerSession,
        GroomingFactory $groomingFactory,
        CreateServiceBookingAsOrder $actionToCreateOrder

    ) {
        $this->config = $config;
        $this->customer = $customer;
        $this->vetSession = $vetSession;
        $this->grooming = $groomingFactory;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->actionToCreateOrder = $actionToCreateOrder;
        $this->key_id = $this->config->getConfigData(Config::KEY_PUBLIC_KEY);
        $this->key_secret = $this->config->getConfigData(Config::KEY_PRIVATE_KEY);
        $this->rzp = new Api($this->key_id, $this->key_secret);
        parent::__construct($context);
    }

    public function execute()
    {
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/vetBookPayNow.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('-----------------------(Book...Online)--------------------------------');
        try {
            $razorconfig = $this->config;
            $postData = $this->getRequest()->getParams();
            $logger->debug(var_export($postData, true));
            $consultSession = $this->vetSession->getVet();
            if(!empty($postData['id'])) {
                $booking = $this->grooming->create()->load($postData['id']);
                if(!empty($postData['razorpay_payment_id']) && ($booking->getRazorpayOrderId() == $postData['razorpay_order_id'])) {
                    $logger->info('-----------------------(Transaction ID)--------------------------------');
                    if(!$this->customerSession->isLoggedIn() && !empty($booking->getCustomerId())) {
                        $customer = $this->customer->load($booking->getCustomerId());
                        $this->customerSession->setCustomerAsLoggedIn($customer);
                    }
                    $transactionId = $postData['razorpay_payment_id'];
                    $logger->debug(var_export($transactionId, true));
                    $secret = $razorconfig->getConfigData('key_secret');
                    $orderId = $booking->getRazorpayOrderId();
                    $payload = $orderId . '|' . $postData['razorpay_payment_id'];
                    $expectedSignature = hash_hmac('sha256', $payload, $secret);
                    if ($expectedSignature == $postData['razorpay_signature']) {
                        $logger->info('-----------------------(MATCHED)--------------------------------');
                        $booking = $this->grooming->create()->load($consultSession['booking_id']);
                        $this->actionToCreateOrder->createIntoOrder($booking);
                        if ($booking) {
                            $logger->info('--------------(UpdateBooking)----------------------');
                            $booking->setPaymentTransId($transactionId)->setBookingStatus('Scheduled')->setPaymentStatus('Paid')->setVisibility(2)->save();
                            $logger->info('-----------------------(successful)--------------------------------');
                            return $this->_redirect('services/vet/success/id/'.$booking->getEntityId());
                        }
                    }
                } else if (isset($postData['error']) && isset($postData['error']['metadata'])){
                    $metaDataBack = json_decode($postData['error']['metadata']);
                    if (isset($metaDataBack->order_id) && ($booking->getRazorpayOrderId() == $metaDataBack->order_id)) {
                        if(!$this->customerSession->isLoggedIn() && !empty($booking->getCustomerId())) {
                            $customer = $this->customer->load($booking->getCustomerId());
                            $this->customerSession->setCustomerAsLoggedIn($customer);
                        }
                        $this->messageManager->addError(__('Payment Failed.'));
                        return $this->_redirect('services/vet/consulting');
                    }
                }
            }
            $this->messageManager->addError(__('Payment Failed.'));
            return $this->_redirect('/');
        } catch (\Exception $e) {
            $logger->info('-----------------------Exception-------------------------------');
            $logger->debug(var_export($e->getMessage(), true));
        }
        $logger->info('-----------------------(Went wrong.)--------------------------------');
        $this->messageManager->addWarning( __('Something went wrong. Please try again') );
        return $this->_redirect('/');
    }
}
