<?php

namespace Zigly\Wallet\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\CustomerFactory;
use Zigly\Wallet\Model\WalletFactory;
use Zigly\Wallet\Helper\Data as WalletHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Customer\Model\ResourceModel\CustomerFactory as CustomerResourceFactory;

class Cashback
{
    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @var WalletHelper
     */
    private $walletHelper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param WalletFactory $walletFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        WalletFactory $walletFactory,
        WalletHelper $walletHelper,
        CollectionFactory $orderCollection,
        SerializerInterface $serializer,
        ScopeConfigInterface $scopeConfig,
        PriceCurrencyInterface $priceCurrency,
        CustomerFactory $customerFactory,
        CustomerResourceFactory $customerResourceFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->priceCurrency = $priceCurrency;
        $this->serializer = $serializer;
        $this->orderCollection = $orderCollection;
        $this->walletFactory = $walletFactory;
        $this->walletHelper = $walletHelper;
        $this->customerFactory = $customerFactory;
        $this->customerResourceFactory = $customerResourceFactory;
    }


    public function execute()
    {
        $orders = $this->orderCollection->create()->addFieldToSelect(
                ['entity_id', 'customer_id', 'cashback','order_type', 'status', 'completed_at', 'discount_amount', 'zwallet', 'shipping_amount', 'completed_at', 'grand_total']
            )->addFieldToFilter(
                'cashback',
                ['neq' => NULL]
            )->addFieldToFilter(
                'cashback_applied',
                ['null' => true]
            )->addFieldToFilter(
                'status',
                ['in' => ['complete']]
            );
        if (count($orders)) {
            foreach($orders as $order) {
                $this->applyCashback($order);
            }
        }
        return $this;
    }

    /**
    * Cashback on purchase
    */
    public function applyCashback($order)
    {
        $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $currentDate = $now->format('Y-m-d');
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/CashbackCron.log');
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/CashbackCron'.$currentDate .'.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('---------------------START---------------------------');
        $logger->info('OrderID: '.$order->getEntityId());
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->create('Magento\Framework\App\ResourceConnection');
        $connection  = $resource->getConnection();
        $data = ["cashback_applied" => "1"];
        $where = ['entity_id = ?' => (int)$order->getEntityId()];
        $tableName = $connection->getTableName("sales_order");
        try {
            $calculatedCashbackDetail = $this->serializer->unserialize($order->getCashback());
            $calculatedCashback = 0;
            $customerId = $order->getCustomerId();
            if (!$customerId) {
                $logger->info('Customer Doesn\'t exists');
                $connection->update($tableName, $data, $where);
            } else {
                    $logger->info('else');
                try {
                    $customer = $this->customerFactory->create()->load($customerId);
                } catch(\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $logger->info('Customer Doesn\'t exists anymore');
                    $connection->update($tableName, $data, $where);
                }
                $walletBalance = is_null($customer->getWalletBalance()) ? "0" : $customer->getWalletBalance();
                $logger->info('getOrderType: '.$order->getOrderType());
                $logger->debug(var_export($calculatedCashbackDetail, true));
                $customerData = $customer->getDataModel();
                if ($order->getOrderType() == 2) {
                    $grandTotal = (float)$order->getGrandTotal();
                    $calculatedCashback = ($calculatedCashbackDetail['percent'] / 100) * $grandTotal;
                    if ((float)$calculatedCashback > (float)$calculatedCashbackDetail['max_amount']) {
                        $logger->info('Max Cashback Limit reached');
                        $calculatedCashback = (float)$calculatedCashbackDetail['max_amount'];
                    }
                } else if ($order->getOrderType() == 1) {
                    $notYet = false;
                    $orderItems = $order->getAllItems();
                    $calculatedItemAmount = 0;
                    foreach ($orderItems as $item) {
                        if (!empty($item->getReturnPolicyDate())) {
                            $returnPolicyEndDate = new \DateTime($item->getReturnPolicyDate());
                            $logger->info('returnPolicyEndDate DateTime');
                            $logger->debug(var_export($returnPolicyEndDate, true));
                            if ($returnPolicyEndDate > $now) {
                                $notYet = true;
                                $logger->info('Break Time Remains');
                                break;
                            }
                        } else {
                            $returnPolicyEndDate = new \DateTime($order->getCompletedAt());
                            $returnPolicyEndDate->add(new \DateInterval('P7D'));
                            $logger->info('completedAtEndDate');
                            $logger->debug(var_export($returnPolicyEndDate, true));
                            if ($returnPolicyEndDate > $now) {
                                $notYet = true;
                                $logger->info('Break Time Remains');
                                break;
                            }
                        }
                        if ((int)$item->getQtyCanceled() <= 0 && (int)$item->getQtyReturned() <= 0) {
                            $calculatedItemAmount += $item->getRowTotal();
                        } else {
                            $logger->info('Item Canceled or Returned');
                        }
                    }

                    $logger->info('calculatedItemAmount'.$calculatedItemAmount);
                    // var_dump($notYet);
                    // var_dump($calculatedItemAmount);
                    //         var_dump(abs($order->getDiscountAmount()));
                    //         // var_dump((int)abs($order->getDiscountAmount()));
                    //         var_dump($order->getZwallet());
                    //         // var_dump(!empty($order->getZwallet()));
                    //         var_dump($order->getShippingAmount());
                    //         // var_dump((int)$order->getShippingAmount > 0);
                    //         var_dump($order->getCompletedAt());
                    if (!$notYet) { //comment
                        if (abs($order->getDiscountAmount()) > 0) {
                            $logger->info('getDiscountAmount'.abs($order->getDiscountAmount()));
                            $calculatedItemAmount -= (float)abs($order->getDiscountAmount());
                        }
                        if (!empty($order->getZwallet())) {
                            $zwallet = $this->serializer->unserialize($order->getZwallet());
                            if ($zwallet['applied'] == true) {
                                $logger->info('walletAmount'.$zwallet['spend_amount']);
                                $calculatedItemAmount -= (float)$zwallet['spend_amount'];
                            }
                        }
                        if ((float)$order->getShippingAmount() > 0) {
                            $logger->info('SHIPAMOUNT'.$order->getShippingAmount());
                            $calculatedItemAmount += (float)$order->getShippingAmount();
                        }
                        $logger->info('Final calculatedItemAmount'.$calculatedItemAmount);

                        if ($calculatedItemAmount > 0 && $order->getCashback()) {
                            $calculatedCashback = ($calculatedCashbackDetail['percent'] / 100) * $calculatedItemAmount;

                            if ((float)$calculatedCashback > (float)$calculatedCashbackDetail['max_amount']) {
                                $logger->info('Max Cashback Limit reached');
                                $calculatedCashback = (float)$calculatedCashbackDetail['max_amount'];
                            }

                            $logger->info('calculatedCashback'.$calculatedCashback);
                        }
                    }
                }

                $logger->info('calculatedCashback: '.$calculatedCashback);
                if ((int)$calculatedCashback > 0) {
                    $updateBalance = $calculatedCashback + $walletBalance;
                    $logger->info('updateBalance: '.$updateBalance);
                    $customerData->setCustomAttribute('wallet_balance', floor($updateBalance));
                    $customer->updateData($customerData);
                    $logger->info('updateData');
                    $customerResource = $this->customerResourceFactory->create();
                            $customerResource->saveAttribute($customer, 'wallet_balance');
                    // $resource = $this->customerFactory->create()->getResource();
                    // $resource->saveAttribute($customer, 'wallet_balance');
                    $logger->info('beforesetCashbackApplied');
                   
                    $data = ["cashback_applied" => "1"];
                    $where = ['entity_id = ?' => (int)$order->getEntityId()];
                    $tableName = $connection->getTableName("sales_order");
                    $connection->update($tableName, $data, $where);
                    // $order->setCashbackApplied('1')->save();
                    $logger->info('aftersetCashbackApplied------------');
                    // Wallet History
                    $walletHistory = $this->walletFactory->create();
                    $data['comment'] = 'Cashback on Transaction';
                    if ($calculatedCashbackDetail['type'] == '1') { //!empty($calculatedCashbackDetail['type']) &&
                        $data['comment'] = "Cashback on 1st Transaction";
                    } elseif ($calculatedCashbackDetail['type'] == '2') {
                        $data['comment'] = "Cashback on 2st Transaction";
                    }
                    $data['amount'] = $calculatedCashback;
                    $data['flag'] = 1;
                    $data['performed_by'] = "system";
                    $data['visibility'] = 1;
                    $data['customer_id'] = $customer->getEntityId();
                    $data['transaction_id'] = $order->getEntityId();
                    $walletHistory->setData($data);
                    $walletHistory->save();
                    // Notification
                    $rechargeVal = array('amount' => $this->currency(floor($calculatedCashback), true, false));
                    $templateVariable['data'] = $rechargeVal;
                    $templateVariable['email'] = $customer->getEmail();
                    $templateVariable['template_id'] = 'wallet/wallet_notification/wallet_recharge_email';
                    $this->walletHelper->sendMail($templateVariable);
                    $rechargeVar['amount'] = floor($calculatedCashback).' INR';
                    $rechargeVar['mobileNo'] = $customer->getPhoneNumber();
                    $rechargeVar['templateid'] = 'wallet/wallet_notification/wallet_recharge_sms';
                    $this->walletHelper->sendRechargeSms($rechargeVar);
                }
            }
        } catch (\Exception $e) {
            $logger->info('Exception');
            $logger->info($e->getMessage());
        }
        //     $logger->info($e->getMessage());
        //     $logger->info(print_r($e->getData(), true));
        // }
        $logger->info('---------------------END---------------------------');
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