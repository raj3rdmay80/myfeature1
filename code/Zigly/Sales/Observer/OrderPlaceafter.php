<?php
/**
* Copyright (C) 2020  Zigly
* @package   Zigly_Sales
*/
namespace Zigly\Sales\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\HTTP\Client\Curl;
use Zigly\Groomer\Model\GroomerFactory;
use Magento\Customer\Model\CustomerFactory;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\CategoryFactory;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Zigly\Wallet\Helper\Data as WalletHelper;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class OrderPlaceafter implements \Magento\Framework\Event\ObserverInterface
{

    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @var GroomingFactory
     */
    protected $groomingFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $customerRepositoryInterface;

    /**
     * @var WalletHelper
     */
    private $walletHelper;

    /** @var InvoiceService */
    protected $invoiceService;

    /** @var Transaction */
    protected $transaction;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;


    /**
    * MSG91 authkey config path
    */
    const XML_PATH_MSG_AUTHKEY = 'msggateway/general/authkey';

    /**
     * @param Customer $customer
     * @param WalletFactory $walletFactory
     * @param ProductFactory $productFactory
     * @param CategoryFactory $categoryFactory
     * @param SerializerInterface $serializer
     * @param ScopeConfigInterface $scopeConfig
     * @param GroomingFactory $GroomingFactory
     * @param CustomerFactory $customerFactory
     * @param GroomerFactory $groomerFactory
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Curl $curl,
        WalletFactory $walletFactory,
        ProductFactory $productFactory,
        CategoryFactory $categoryFactory,
        SerializerInterface $serializer,
        CustomerFactory $customerFactory,
        GroomingFactory $groomingFactory,
        WalletHelper $walletHelper,
        InvoiceService $invoiceService,
        Transaction $transaction,
        GroomerFactory $groomerFactory,
        ScopeConfigInterface $scopeConfig,
        PriceCurrencyInterface $priceCurrency,
        CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->curl = $curl;
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
        $this->grooming = $groomingFactory;
        $this->priceCurrency = $priceCurrency;
        $this->walletFactory = $walletFactory;
        $this->productFactory = $productFactory;
        $this->categoryFactory = $categoryFactory;
        $this->walletHelper = $walletHelper;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->groomerFactory = $groomerFactory;
        $this->customerFactory = $customerFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $customerId = $order->getCustomerId();
        $name = $order->getCustomerFirstname().' '.$order->getCustomerLastname();
        // $customer = $this->customerRepositoryInterface->getById($customerId);
        $customer = $this->customerFactory->create()->load($customerId);
        $mobileNumber = $customer->getPhoneNumber();$now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $this->checkCashback($order, $customer);
        $this->sendSuccessfullPaymentSms($order, $customer);
        $mobileNo = '';
        if(null !== $mobileNumber){
            $mobileNo = '91'.$mobileNumber;
        }
        if (!empty($order->getZwallet())) {
            $zWallet = $this->serializer->unserialize($order->getZwallet());
            
            if ($zWallet['applied'] == true) {
                $model = $this->walletFactory->create();
                if ($order->getOrderType() == 2) {
                    $data['comment'] = "Service Booked";
                } else {
                    $data['comment'] = "Product Purchased";
                }
                $data['amount'] = $zWallet['spend_amount'];
                $data['flag'] = 0;
                $data['performed_by'] = "customer";
                $data['visibility'] = 1;
                $data['customer_id'] = $customerId;
                $data['transaction_id'] = $order->getEntityId();
                $model->setData($data);
                $model->save();
                /*$customerModel = $this->customerFactory->create()->load($customerId);*/
                $customerData = $customer->getDataModel();
                $totalBalance = is_null($customer->getWalletBalance()) ? "0" : $customer->getWalletBalance();
                $balance = $totalBalance - $zWallet['spend_amount'];
                $balance = abs($balance);
                $customerData->setCustomAttribute('wallet_balance',$balance);
                $customer->updateData($customerData);
                $this->customerFactory->create()->getResource()->saveAttribute($customer, 'wallet_balance');
            }
        }
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue('msggateway/sales_orders_configuration/order_success_sms', $storeScope);
        $senderName = $this->scopeConfig ->getValue('msggateway/sales_orders_configuration/orders_sender_name', $storeScope);
        $incrementId = $order->getIncrementId();
        if(is_numeric($mobileNo) && $smstemplateid){
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\",\n  \"order\": \"".$incrementId."\"}",
              CURLOPT_HTTPHEADER => array(
                "authkey: ".$authkey."",
                "content-type: application/JSON"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
            if ($err) {
                $optputmsg = "cURL Error #:" . $err;
            }
        }
        $this->captureCancelReturnPolicy($order);
        $this->generateInvoiceForServicesPaid($order);
        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();
        $methodTitle = $method->getTitle();
        if ($methodTitle == 'Razorpay' && !empty($order->getZwallet())) {
            $order->setTotalPaid($order->getGrandTotal())->save();
            return $this;
        }
    }

    public function getMsgauthkey()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_MSG_AUTHKEY, $storeScope);
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

    /**
    * Capture Cancel Return Policy from Category
    */
    public function captureCancelReturnPolicy($order)
    {
        $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $currentDate = $now->format('Y-m-d');
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/CaptureCancelReturnPolicy'.$currentDate .'.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('---------------------START---------------------------');
        try {
            $orderItems = $order->getAllItems();
            foreach ($orderItems as $item) {
                $product = $this->productFactory->create()->load($item->getProductId());
                if ($product) {
                    $logger->info('product:'.$product->getEntityId());
                    $policyCategory = null;
                    $categoryIds = $product->getCategoryIds();
                    $logger->info('categoryIds');
                    $logger->debug(var_export($categoryIds, true));
                    foreach($categoryIds as $category){
                        $loadedCategory = $this->categoryFactory->create()->load($category);
                        if ($loadedCategory->getLevel() == 4) {
                            $logger->info('LEVEL4');
                            $logger->info('LoadedCategory:'.$loadedCategory->getEntityId());
                            $policyCategory = $loadedCategory;
                        }
                        if ($loadedCategory->getLevel() == 5) {
                            $logger->info('LEVEL5');
                            $logger->info('LoadedCategory:'.$loadedCategory->getEntityId());
                            $policyCategory = $loadedCategory;
                            break;
                        }
                    }
                    if ($policyCategory) {
                        $logger->info('GET from policyCategory');
                        if ($policyCategory->getCancelPolicy()) {
                            $logger->info('setCancelPolicy'.$policyCategory->getCancelPolicy());
                            $itemCreatedAt = new \DateTime($item->getCreatedAt());
                            $addDays = 'P'.$policyCategory->getCancelPolicy().'D';
                            $itemCreatedAt->add(new \DateInterval($addDays));
                            $logger->info('setCancelPolicyDATE'.$itemCreatedAt->format('Y-m-d H:i:s'));
                            $item->setCancelPolicy($policyCategory->getCancelPolicy());
                            $item->setCancelPolicyDate($itemCreatedAt->format('Y-m-d H:i:s'));
                        }
                        if ($policyCategory->getReturnPolicy()) {
                            $logger->info('setReturnPolicy'.$policyCategory->getReturnPolicy());
                            // $itemCreatedAt = new \DateTime($item->getCreatedAt());
                            // $addDays = 'P'.$policyCategory->getReturnPolicy().'D';
                            // $itemCreatedAt->add(new \DateInterval($addDays));
                            // $logger->info('setReturnPolicyDATE'.$itemCreatedAt->format('Y-m-d H:i:s'));
                            $item->setReturnPolicy($policyCategory->getReturnPolicy());
                        }
                        $item->save();
                    }
                }
            }
        } catch (\Exception $e) {
            $logger->info('Exception: '. $e->getMessage());
        }
        $logger->info('---------------------END---------------------------');
    }

    /**
    * Cashback on purchase
    */
    public function checkCashback($order, $customer)
    {
        $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $currentDate = $now->format('Y-m-d');
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/WalletCashback'.$currentDate .'.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('---------------------START---------------------------');
        $logger->info('CustomerID: '.$customer->getEntityId());
        $logger->info('OrderID: '.$order->getEntityId());
        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();
        $methodTitle = $method->getTitle();
        $logger->info('Payment Method: '.$methodTitle);

        try {
            if ($methodTitle == 'Razorpay' && $this->walletHelper->isEnabled()) {
                $customerData = $customer->getDataModel();

                $firstCashback['percent'] = $this->walletHelper->getConfig('wallet/cashback_first/percentage');
                $firstCashback['start_date'] = $this->walletHelper->getConfig('wallet/cashback_first/start_date');
                $firstCashback['end_date'] = $this->walletHelper->getConfig('wallet/cashback_first/end_date');
                $firstCashback['min_amount'] = $this->walletHelper->getConfig('wallet/cashback_first/min_amount');
                $firstCashback['max_cashback'] = $this->walletHelper->getConfig('wallet/cashback_first/max_cashback');
                $secondCashback['percent'] = $this->walletHelper->getConfig('wallet/cashback_second/percentage');
                $secondCashback['start_date'] = $this->walletHelper->getConfig('wallet/cashback_second/start_date');
                $secondCashback['end_date'] = $this->walletHelper->getConfig('wallet/cashback_second/end_date');
                $secondCashback['min_amount'] = $this->walletHelper->getConfig('wallet/cashback_second/min_amount');
                $secondCashback['max_cashback'] = $this->walletHelper->getConfig('wallet/cashback_second/max_cashback');
                $logger->info('FirstCashback Config');
                $logger->debug(var_export($firstCashback, true));
                $logger->info('SecondCashback Config');
                $logger->debug(var_export($secondCashback, true));

                $grandTotal = (float)$order->getGrandTotal();
                $logger->info('Order Total: '.$grandTotal);

                $walletCashbacked = $customer->getWalletCashback();
                $walletBalance = is_null($customer->getWalletBalance()) ? "0" : $customer->getWalletBalance();
                $logger->info('walletCashbacked: '.$walletCashbacked);
                $logger->info('walletBalance: '.$walletBalance);

                if (empty($walletCashbacked)) {
                    $logger->info('So far no cashback');
                    if (($firstCashback['start_date'] <= $currentDate) && ($currentDate <= $firstCashback['end_date']) && ($grandTotal >= (float)$firstCashback['min_amount'])) {
                        $logger->info('Eligible for FirstCashback');
                        $firstCashbackPercent = (!empty($firstCashback['percent'])) ? (float)$firstCashback['percent'] : 0;
                        $calculatedCashback = ($firstCashbackPercent / 100) * $grandTotal;
                        $logger->info('calculatedCashback: '.$calculatedCashback);
                        if ($calculatedCashback > 0) {
                            // if ((float)$calculatedCashback > (float)$firstCashback['max_cashback']) {
                            //     $logger->info('Max Cashback Limit reached');
                            //     $calculatedCashback = (float)$firstCashback['max_cashback'];
                            //     $logger->info('calculatedCashbackMAX: '.$calculatedCashback);
                            // }
                            $calculatedCashback = [
                                'percent' => $firstCashbackPercent,
                                'max_amount' => $firstCashback['max_cashback'],
                                'type' => '1'
                            ];
                            $order->setCashback($this->serializer->serialize($calculatedCashback))->save();
                            // $order->save();

                            // $updateBalance = $calculatedCashback + $walletBalance;
                            // $customerData->setCustomAttribute('wallet_balance', floor($updateBalance));
                            $customerData->setCustomAttribute('wallet_cashback', 1);
                            $customer->updateData($customerData);
                            // $this->customerFactory->create()->getResource()->saveAttribute($customer, 'wallet_balance');
                            $this->customerFactory->create()->getResource()->saveAttribute($customer, 'wallet_cashback');
                            // $logger->info('updateBalance: '.$updateBalance);
                            // $walletHistory = $this->walletFactory->create();
                            // $data['comment'] = "Cashback on 1st Transaction";
                            // $data['amount'] = $calculatedCashback;
                            // $data['flag'] = 1;
                            // $data['performed_by'] = "system";
                            // $data['visibility'] = 1;
                            // $data['customer_id'] = $customer->getEntityId();
                            // $data['transaction_id'] = $order->getEntityId();
                            // $walletHistory->setData($data);
                            // $walletHistory->save();
                            // notification
                            // $rechargeVal = array('amount' => $this->currency(floor($calculatedCashback), true, false));
                            // $templateVariable['data'] = $rechargeVal;
                            // $templateVariable['email'] = $customer->getEmail();
                            // $templateVariable['template_id'] = 'wallet/wallet_notification/wallet_recharge_email';
                            // $this->walletHelper->sendMail($templateVariable);
                            // $rechargeVar['amount'] = floor($calculatedCashback).' INR';
                            // $rechargeVar['mobileNo'] = $customer->getPhoneNumber();
                            // $rechargeVar['templateid'] = 'wallet/wallet_notification/wallet_recharge_sms';
                            // $this->walletHelper->sendRechargeSms($rechargeVar);
                        }
                    } else {
                        $logger->info('Not Eligible for FirstCashback');
                    }
                } else if ($walletCashbacked == 1) {
                    $logger->info('1st applied Already. Checking for 2nd');
                    if (($secondCashback['start_date'] <= $currentDate) && ($currentDate <= $secondCashback['end_date']) && ($grandTotal >= (float)$secondCashback['min_amount'])) {
                        $logger->info('Eligible for SecondCashback');
                        $secondCashbackPercent = (!empty($secondCashback['percent'])) ? (float)$secondCashback['percent'] : 0;
                        $calculatedCashback = ($secondCashbackPercent / 100) * $grandTotal;
                        $logger->info('calculatedCashback: '.$calculatedCashback);
                        if ($calculatedCashback > 0) {
                            // if ((float)$calculatedCashback > (float)$secondCashback['max_cashback']) {
                            //     $logger->info('Max Cashback Limit Reached');
                            //     $calculatedCashback = (float)$secondCashback['max_cashback'];
                            //     $logger->info('calculatedCashbackMAX: '.$calculatedCashback);
                            // }
                            $calculatedCashback = [
                                'percent' => $secondCashbackPercent,
                                'max_amount' => $secondCashback['max_cashback'],
                                'type' => '2'
                            ];
                            $order->setCashback($this->serializer->serialize($calculatedCashback))->save();
                            // $updateBalance = $calculatedCashback + $walletBalance;
                            // $customerData->setCustomAttribute('wallet_balance', floor($updateBalance));
                            $customerData->setCustomAttribute('wallet_cashback', 2);
                            $customer->updateData($customerData);
                            // $this->customerFactory->create()->getResource()->saveAttribute($customer, 'wallet_balance');
                            $this->customerFactory->create()->getResource()->saveAttribute($customer, 'wallet_cashback');
                            // $logger->info('updateBalance: '.$updateBalance);
                            // $walletHistory = $this->walletFactory->create();
                            // $data['comment'] = "Cashback on 2st Transaction";
                            // $data['amount'] = $calculatedCashback;
                            // $data['flag'] = 1;
                            // $data['performed_by'] = "system";
                            // $data['visibility'] = 1;
                            // $data['customer_id'] = $customer->getEntityId();
                            // $data['transaction_id'] = $order->getEntityId();
                            // $walletHistory->setData($data);
                            // $walletHistory->save();
                            // $rechargeVal = array('amount' => $this->currency(floor($calculatedCashback), true, false));
                            // $templateVariable['data'] = $rechargeVal;
                            // $templateVariable['email'] = $customer->getEmail();
                            // $templateVariable['template_id'] = 'wallet/wallet_notification/wallet_recharge_email';
                            // $this->walletHelper->sendMail($templateVariable);
                            // $rechargeVar['amount'] = floor($calculatedCashback).' INR';
                            // $rechargeVar['mobileNo'] = $customer->getPhoneNumber();
                            // $rechargeVar['templateid'] = 'wallet/wallet_notification/wallet_recharge_sms';
                            // $this->walletHelper->sendRechargeSms($rechargeVar);
                        }
                    } else {
                        $logger->info('Not Eligible for SeconCashback');
                    }
                }
            }
        } catch (\Exception $e) {
            $logger->info('Catch: '. $e->getMessage());
        }
        $logger->info('---------------------END---------------------------');
    }

    /*
    * send successfull payment msg
    */
    public function sendSuccessfullPaymentSms($order, $customer)
    {
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/paymentSuccess.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('---------------------START---------------------------');
        $logger->info('CustomerID: '.$customer->getEntityId());
        $logger->info('OrderID: '.$order->getEntityId());
        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();
        $methodTitle = $method->getTitle();
        $logger->info('Payment Method: '.$methodTitle);
        try {
            if ($methodTitle == 'Razorpay') {
                $authkey = $this->getMsgauthkey();
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $smstemplateid = $this->scopeConfig->getValue('msggateway/order_payment/order_success_payment_sms', $storeScope);
                $senderName = $this->scopeConfig ->getValue('msggateway/order_payment/success_payment_sender_name', $storeScope);
                $mobileNumber = $customer->getPhoneNumber();
                $mobileNo = '';
                if(null !== $mobileNumber) {
                    $mobileNo = '91'.$mobileNumber;
                }
                $path = "https://bit.ly/2W3FKUS";
                if(is_numeric($mobileNo) && $smstemplateid){
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                      CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\",\n  \"url\": \"".$path."\"}",
                      CURLOPT_HTTPHEADER => array(
                        "authkey: ".$authkey."",
                        "content-type: application/JSON"
                      ),
                    ));
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);
                }
            }
        } catch (\Exception $e) {
            $logger->info('Catch: '. $e->getMessage());
        }
        $logger->info('---------------------END---------------------------');
    }

    /**
    * Capture Cancel Return Policy from Category
    */
    public function generateInvoiceForServicesPaid($order)
    {
        $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $currentDate = $now->format('Y-m-d');

        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/generateInvoiceForServicesPaid-'.$currentDate.'.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('---------------------START---------------------------');
        try {
            $payment = $order->getPayment();
            $method = $payment->getMethodInstance();
            $methodCode = $method->getCode();
            $logger->info('TITLE'.$methodCode );

            if ($methodCode == "services_razorpay") {
                if ($order->canInvoice()) {
                    $invoice = $this->invoiceService->prepareInvoice($order);
                    $invoice->register();
                    $invoice->save();
                    $transactionSave = $this->transaction->addObject(
                        $invoice
                    )->addObject(
                        $invoice->getOrder()
                    );
                    $transactionSave->save();
                    $logger->info('---------------------SAVED---------------------------');
                    // $this->invoiceSender->send($invoice);
                    // $order->addStatusHistoryComment(
                    //     __('Notified customer about invoice creation #%1.', $invoice->getId())
                    // )
                    //     ->setIsCustomerNotified(true)
                    //     ->save();
                }
            }
        } catch (\Exception $e) {
            $logger->info('Exception: '. $e->getMessage());
        }
    }
}