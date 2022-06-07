<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Controller\Grooming;

use Magento\Framework\App\Action\Context;
use Razorpay\Api\Api;
use Razorpay\Magento\Model\Config;
use Zigly\GroomingService\Model\Session;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Zigly\GroomingService\Model\GroomingFactory;
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
    protected $groomingSession;

    /**
     * @var GroomingFactory
     */
    protected $groomingFactory;

    /** @var CreateServiceBookingAsOrder */
    protected $actionToCreateOrder;

    /**
     * Customer session model
     *
     * @var CustomerSession
     */
    protected $customerSession;

     /**
     * @param Context     $context
     * @param GroomingFactory $GroomingFactory
     * @param Config $config
     * @param Customer $customer
     * @param Session $groomingSession
     * @param CustomerSession $customerSession
     * @param CustomerFactory $customerFactory
     * @param CreateServiceBookingAsOrder $actionToCreateOrder
     */
    public function __construct(
        Context $context,
        Config $config,
        Session $groomingSession,
        Customer $customer,
        CustomerFactory $customerFactory,
        CustomerSession $customerSession,
        CreateServiceBookingAsOrder $actionToCreateOrder,
        GroomingFactory $groomingFactory

    ) {
        $this->config = $config;
        $this->customer = $customer;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->key_id = $this->config->getConfigData(Config::KEY_PUBLIC_KEY);
        $this->key_secret = $this->config->getConfigData(Config::KEY_PRIVATE_KEY);
        $this->rzp = new Api($this->key_id, $this->key_secret);
        $this->groomingSession = $groomingSession;
        $this->actionToCreateOrder = $actionToCreateOrder;
        $this->grooming = $groomingFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/groomBookPayNow.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('-----------------------(Book...Online)--------------------------------');

        try {
            $razorconfig = $this->config;
            $postData = $this->getRequest()->getParams();
            $logger->debug(var_export($postData, true));
            $groomSession = $this->groomingSession->getGroomService();
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
                    $logger->debug(var_export($groomSession, true));
                    $secret = $razorconfig->getConfigData('key_secret');
                    $orderId = $booking->getRazorpayOrderId();
                    $payload = $orderId . '|' . $postData['razorpay_payment_id'];
                    $expectedSignature = hash_hmac('sha256', $payload, $secret);
                    if ($expectedSignature == $postData['razorpay_signature']) {
                        $logger->info('-----------------------(MATCHED)--------------------------------');
                        // $booking = $this->grooming->create()->load($groomSession['booking_id']);
                        $this->actionToCreateOrder->createIntoOrder($booking);
                        if ($booking) {
                            $logger->info('--------------(UpdateBooking)----------------------');
                            $booking->setPaymentTransId($transactionId)->setBookingStatus('Scheduled')->setPaymentStatus('Paid')->setVisibility(2)->save();
                            $logger->info('-----------------------(successful)--------------------------------');
                            /*$this->messageManager->addSuccess( __('Paid successfully.') );*/
                            return $this->_redirect('services/grooming/success/id/'.$booking->getEntityId());
                        }
                    }
                } else if (isset($postData['error']) && isset($postData['error']['metadata'])){
                    $metaDataBack = json_decode($postData['error']['metadata']);
                    if (isset($metaDataBack->order_id) && ($booking->getRazorpayOrderId() == $metaDataBack->order_id)) {
                        // $booking = $this->grooming->create()->load($metaDataBack->order_id, 'razorpay_order_id');
                        if(!$this->customerSession->isLoggedIn() && !empty($booking->getCustomerId())) {
                            $customer = $this->customer->load($booking->getCustomerId());
                            $this->customerSession->setCustomerAsLoggedIn($customer);
                        }
                        $this->messageManager->addError(__('Payment Failed.'));
                        return $this->_redirect('services/grooming');
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
        return $this->_redirect('services/grooming/');
    }
}
