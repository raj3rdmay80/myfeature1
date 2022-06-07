<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Controller\Index;

use Razorpay\Magento\Model\Config;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Razorpay\Magento\Controller\Payment\Order as Razor;

class Save extends \Magento\Framework\App\Action\Action
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
     * Customer session model
     *
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Razor
     */
    protected $razor;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     *
     * @param Razor $razor
     * @param Config $config
     * @param Context  $context
     * @param WalletFactory $walletFactory
     * @param JsonFactory $jsonResultFactory
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Razor $razor,
        Config $config,
        Context $context,
        WalletFactory $walletFactory,
        JsonFactory $jsonResultFactory,
        CustomerSession $customerSession
    ) {
        $this->razor = $razor;
        $this->config = $config;
        $this->walletFactory = $walletFactory;
        $this->customerSession = $customerSession;
        $this->jsonResultFactory = $jsonResultFactory;
        parent::__construct($context);
    }

    /**
     * Execute save action
     * @return void
     */
    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/wallet.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('-----------------------(Wallet...)--------------------------------');
        $amountData = $this->getRequest()->getPostValue();
        $result = $this->jsonResultFactory->create();
        $customerId = $this->customerSession->getCustomerId();
        $customer = $this->customerSession->getCustomer();

        $model = $this->walletFactory->create();
        $data['comment'] = "Recharge Wallet";
        $data['amount'] = $amountData['mode'];
        $data['flag'] = "1";
        $data['performed_by'] = "customer";
        $data['customer_id'] = $customer->getId();
        $model->setData($data);
        $model->save();
        $responseData = [
            'success' => false,
            'message' => 'Something went wrong. Please reload and try again'
        ];

        if (empty($amountData['mode'])) {
            $responseData['message'] = 'Please select a payment.';
            $result->setData($responseData);
            return $result;
        }
        try {
            $logger->info('-----------------------data-------------------------------');
            $logger->debug(var_export($model->getWalletId(), true));
            if (!empty($amountData['mode'])) {
                $payment = array("amount" => $amountData['mode'], "id" => $model->getWalletId());
                $responseData['success'] = true;
                $responseData['message'] = '';
                if ($amountData['mode']) {
                    $responseData['razorData']['razorId'] = $this->razororderId($payment);
                    $responseData['razorData']['orderId'] = 'W_'.$model->getWalletId();
                    $responseData['razorData']['amount'] = (int) (number_format($amountData['mode'] * 100, 0, ".", ""));
                    $responseData['razorConfig']['key'] = $this->getKeyId();
                    $responseData['razorConfig']['name'] = $this->getMerchantNameOverride();
                    $responseData['razorConfig']['customerName'] = $customer->getFirstname();
                    $responseData['razorConfig']['customerEmail'] = $customer->getEmail();
                    $responseData['razorConfig']['customerPhoneNo'] = $customer->getPhoneNumber();
                    $razorId = $responseData['razorData']['razorId'];
                    $this->customerSession->setWalletRazorId($razorId);
                    $this->customerSession->setWalletId($model->getWalletId());
                }
            }
            $logger->info('-----------------------amount-------------------------------');
            $logger->debug(var_export($amountData['mode'], true));
            $logger->info('-----------------------wallet razor id-------------------------------');
            $logger->debug(var_export($this->customerSession->getWalletRazorId(), true));
        } catch (\Exception $e) {
            $logger->info('-----------------------Exception-------------------------------');
            $logger->debug(var_export($e->getMessage(), true));
            $responseData['trace'] = $e->getMessage();
        }
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
    public function razororderId($payment)
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
            $amount = (int) (number_format($payment['amount'] * 100, 0, ".", ""));
            $order = $this->razor->rzp->order->create([
                'amount' => $amount,
                'receipt' => $payment['id'],
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
                    // 'cart_id'          => $orderId,
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