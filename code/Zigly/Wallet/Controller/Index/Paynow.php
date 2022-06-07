<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Controller\Index;

use Razorpay\Api\Api;
use Zigly\Wallet\Helper\Data;
use Razorpay\Magento\Model\Config;
use Magento\Customer\Model\Customer;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Framework\App\Request\InvalidRequestException;

/**
 * Razor pay callback booking Controller
 */
class Paynow extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{

    /**
     * Customer session model
     *
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

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
     * @param Context $context
     * @param Data $helperData
     * @param Customer $customer
     * @param WalletFactory $walletFactory
     * @param CustomerFactory $customerFactory
     * @param CustomerSession $customerSession
     * @param PriceCurrencyInterface $priceCurrency
     * @param Config $config
     */
    public function __construct(
        Config $config,
        Data $helperData,
        Context $context,
        Customer $customer,
        WalletFactory $walletFactory,
        CustomerFactory $customerFactory,
        CustomerSession $customerSession,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->config = $config;
        $this->customer = $customer;
        $this->helperData = $helperData;
        $this->priceCurrency = $priceCurrency;
        $this->walletFactory = $walletFactory;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->key_id = $this->config->getConfigData(Config::KEY_PUBLIC_KEY);
        $this->key_secret = $this->config->getConfigData(Config::KEY_PRIVATE_KEY);
        $this->rzp = new Api($this->key_id, $this->key_secret);
        parent::__construct($context);
    }

    public function execute()
    {
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/walletPayNow.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('-----------------------(Book...Online)--------------------------------');

        try {
            $razorconfig = $this->config;
            $postData = $this->getRequest()->getPostValue();
            $logger->debug(var_export($postData, true));
            if(!empty($postData['razorpay_payment_id'])) {
                $customerId = $this->customerSession->getCustomerId();
                $logger->info('-----------------------(Transaction ID)--------------------------------');
                $transactionId = $postData['razorpay_payment_id'];
                $logger->debug(var_export($transactionId, true));
                $secret = $razorconfig->getConfigData('key_secret');
                $orderId = $this->customerSession->getWalletRazorId();
                $payload = $orderId . '|' . $postData['razorpay_payment_id'];
                $expectedSignature = hash_hmac('sha256', $payload, $secret);
                if ($expectedSignature == $postData['razorpay_signature']) {
                    $logger->info('-----------------------(MATCHED)--------------------------------');
                    $wallet = $this->walletFactory->create()->load($this->customerSession->getWalletId());
                    if ($wallet) {
                        $logger->info('------------------(Update wallet)--------------------------');
                        $wallet->setVisibility(1)->save();
                        $this->customerSession->unsWalletRazorId();
                        $this->customerSession->unsWalletId();
                        $customer = $this->customer->load($customerId);
                        $customerData = $customer->getDataModel();
                        $totalBalance = is_null($customer->getWalletBalance()) ? "0" : $customer->getWalletBalance();
                        $balance = $wallet->getAmount() + $totalBalance;
                        $logger->info('----------(totalBalance)----------------------');
                        $logger->debug(var_export($totalBalance, true));
                        $logger->info('------------------(balance)------------------');
                        $logger->debug(var_export($balance, true));
                        $customerData->setCustomAttribute('wallet_balance',$balance);
                        $customer->updateData($customerData);
                        $customerResource = $this->customerFactory->create();
                        $customerResource->saveAttribute($customer, 'wallet_balance');
                        $rechargeVal = array('amount' => $this->currency(number_format((float)$wallet->getAmount(), 0, '', ''), true, false));
                        $templateVariable['data'] = $rechargeVal;
                        $templateVariable['email'] = $customer->getEmail();
                        $templateVariable['template_id'] = 'wallet/wallet_notification/wallet_recharge_email';
                        $this->helperData->sendMail($templateVariable);
                        $rechargeVar['amount'] = round($wallet->getAmount()).' INR';
                        $rechargeVar['mobileNo'] = $customer->getPhoneNumber();
                        $rechargeVar['templateid'] = 'wallet/wallet_notification/wallet_recharge_sms';
                        $this->helperData->sendRechargeSms($rechargeVar);
                        $logger->info('-----------------------(successful)--------------------------------');
                        $this->messageManager->addSuccess(__('Recharged successfully.'));
                        return $this->_redirect('wallet/index/index');
                    }
                }
            }
        } catch (\Exception $e) {
            $logger->info('-----------------------Exception-------------------------------');
            $logger->debug(var_export($e->getMessage(), true));
        }
    }

    /**
     * Convert and format price value for current application store
     *
     * @param   float $value
     * @param   bool $format
     * @param   bool $includeContainer
     * @return  float|string
     */
    public function currency($value, $format = true, $includeContainer = true)
    {
        return $format
            ? $this->priceCurrency->convertAndFormat($value, $includeContainer)
            : $this->priceCurrency->convert($value);
    }
}
