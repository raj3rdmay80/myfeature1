<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
namespace Zigly\Sales\Controller\Orderitem;

use Zigly\Sales\Helper\Data;
use Magento\Sales\Model\Order;
use Magento\Customer\Model\Customer;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Customer\Model\SessionFactory as CustomerSessionFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;
use Razorpay\Magento\Model\Config;
use Razorpay\Magento\Controller\Payment\Order as Razor;

class Cancel extends \Magento\Framework\App\Action\Action
{

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var PageFactory
    */
    protected $pageFactory;

    /**
     * Customer session model
     *
     * @var CustomerSessionFactory
     */
    protected $customerSession;

    /**
     * @param Order $order
     * @param Data $helperData
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param PageFactory $resultPageFactory
     * @param Customer $customer
     * @param WalletFactory $walletFactory
     * @param CustomerFactory $customerFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param OrderCommentSender $OrderCommentSender
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerSessionFactory $customerSession
     * @param SerializerInterface $serializer
     * @param OrderManagementInterface $orderManagement
     * @param OrderItemRepositoryInterface $itemsRepository
     */
    public function __construct(
        Order $order,
        Context $context,
        Data $helperData,
        PageFactory $pageFactory,
        Customer $customer,
        WalletFactory $walletFactory,
        SerializerInterface $serializer,
        CustomerFactory $customerFactory,
        JsonFactory $resultJsonFactory,
        PriceCurrencyInterface $priceCurrency,
        CustomerSessionFactory $customerSession,
        ScopeConfigInterface $scopeConfig,
        OrderCommentSender $OrderCommentSender,
        OrderManagementInterface $orderManagement,
        Razor $razor,
        Config $config,
        OrderItemRepositoryInterface $itemsRepository
    ) {
        $this->order = $order;
        $this->helperData = $helperData;
        $this->customer = $customer;
        $this->serializer = $serializer;
        $this->walletFactory = $walletFactory;
        $this->priceCurrency = $priceCurrency;
        $this->customerFactory = $customerFactory;
        $this->scopeConfig = $scopeConfig;
        $this->pageFactory = $pageFactory;
        $this->orderManagement = $orderManagement;
        $this->itemsRepository = $itemsRepository;
        $this->customerSession = $customerSession;
        $this->OrderCommentSender = $OrderCommentSender;
        $this->razor = $razor;
        $this->config = $config;
        $this->resultJsonFactory = $resultJsonFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $result = $this->resultJsonFactory->create();
        $post = $this->getRequest()->getPostValue();
        $now = new \DateTime('now');
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/CancelItem-'.$now->format('d-m-Y').'.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r("--------Start--------", true));
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        try {
            $customerId = $this->customerSession->create()->getCustomerId();
            if (!empty($post['orderid']) && !empty($post['cancelreason']) && !empty($post['itemid'])) {
                $logger->debug(var_export($post, true));
                $orderId = $post['orderid'];
                $comment = $post['cancelreason'];
                $order = $this->order->load($orderId);
                $item = $this->itemsRepository->get($post['itemid']);
                $state = 'canceled';
                $status = 'canceled';
                $status = ['processing', 'pending'];
                if (
                    $order && in_array($order->getStatus(), $status) &&
                    $order->getCustomerId() == $customerId &&
                    $item && $item->getOrderId() == $orderId &&
                    (int)$item->getQtyCanceled() <= 0 
                ) { //&& !empty($item->getCancelPolicyDate())
                    $logger->info('----------------Basic validations passed----------------');

                    $cancelPolicyEndDate = new \DateTime($item->getCancelPolicyDate());
                    // if ($cancelPolicyEndDate > $now) {
                        $logger->info('----------------WIthin policy date----------------');
                        $calculatedItemAmount = 0;
                        $isAllItemsCanceled = 1;
                        foreach ($order->getAllItems() as $orderItem) {
                            if (!empty($orderItem->getParentItemId())) continue;
                            if ($orderItem->getItemId() == $post['itemid']) {
                                $loopedItem = $orderItem;
                                continue;
                            }
                            if ((int)$orderItem->getQtyOrdered() != (int)$orderItem->getQtyCanceled()) {
                                $logger->info('----------------Cannot Cancel whole order----------------');
                                $isAllItemsCanceled = 0;
                                $calculatedItemAmount += $orderItem->getRowTotal();
                            }
                        }
                        $logger->info('ExistingShippingAmount'.$order->getShippingAmount());
                        $logger->info('calculatedItemAmount'.$calculatedItemAmount);
                        if ($calculatedItemAmount > 0) {
                            if (abs($order->getDiscountAmount()) > 0) {
                                $logger->info('getDiscountAmount'.abs($order->getDiscountAmount()));
                                $calculatedItemAmount -= (float)abs($order->getDiscountAmount());
                            }
                            // if (!empty($order->getZwallet())) {
                            //     $zwallet = $this->serializer->unserialize($order->getZwallet());
                            //     if ($zwallet['applied'] == true) {
                            //         $logger->info('walletAmount'.$zwallet['spend_amount']);
                            //         $calculatedItemAmount -= (float)$zwallet['spend_amount'];
                            //     }
                            // }
                            /*if ((float)$order->getShippingAmount() > 0) {
                                $logger->info('SHIPAMOUNT'.$order->getShippingAmount());
                                $calculatedItemAmount += (float)$order->getShippingAmount();
                            }*/
                            $logger->info('Final calculatedItemAmount'.$calculatedItemAmount);
                        }
                        $shipamount = $this->scopeConfig->getValue('carriers/flatrate/price', $storeScope);
                        if ((float)$order->getShippingAmount() <= 0 && $calculatedItemAmount <= 499 && !empty($shipamount) && !$isAllItemsCanceled) {
                            // if (false) {
                            // if ($calculatedItemAmount <= 499) {
                            $logger->info(print_r("--------New FLow Capture Shipping--------", true));
                            $logger->info(print_r("Shipping-Amount".$shipamount, true));
                            $note = [
                                'type' => 'cancel',
                                'itemid' => $post['itemid'],
                                'orderid' => $orderId,
                                'comment' => $comment
                            ];
                            $customer = $this->customerSession->create()->getCustomer();
                            $responseData['razorData']['razorId'] = $this->razororderId((int)$shipamount, 'SHIP'.$order->getIncrementId(), $note);
                            $responseData['razorData']['orderId'] = 'SHIP'.$order->getIncrementId();
                            $responseData['razorData']['amount'] = (int)(number_format((int)$shipamount * 100, 0, ".", ""));
                            $responseData['razorConfig']['key'] = $this->getKeyId();
                            $responseData['razorConfig']['name'] = $this->getMerchantNameOverride();
                            $responseData['razorConfig']['customerName'] = $customer->getFirstname();
                            $responseData['razorConfig']['customerEmail'] = $customer->getEmail();
                            $responseData['razorConfig']['customerPhoneNo'] = $customer->getPhoneNumber();
                            $item->setRazorpayOrderId($responseData['razorData']['razorId'])->save(); //$qty
                            $result->setData($responseData);
                            $logger->info(print_r("--------New FLow END--------", true));
                            return $result;
                        } else {
                            $logger->info('----------------Can CANCEL - OLD FLOW---------------');
                            $loopedItem->setQtyCanceled((int)$loopedItem->getQtyOrdered())->save(); //$qty
                            $calculatedValue = 1;
                            $totalPercent = 0;
                            $spentAmount = 0;
                            if (!empty($order->getZwallet())){
                                $zWallet = $this->serializer->unserialize($order->getZwallet());
                                if ($zWallet['applied'] == true) {
                                    $maxTransactionPercent = (float)$zWallet['percent'];
                                    $spentAmount = floor($zWallet['spend_amount']);
                                    $rowTotalPercent = floor(($maxTransactionPercent / 100) * $item->getRowTotal());
                                    $logger->info(print_r("rowTotalPercent: ".$rowTotalPercent, true));
                                    if ($rowTotalPercent <= 0) {
                                        $calculatedValue = 0;
                                    } else {
                                        if ($spentAmount >= $rowTotalPercent) {
                                            $calculatedValue = $rowTotalPercent;
                                            $totalPercent = $rowTotalPercent;
                                        } else if ($spentAmount < $rowTotalPercent) {
                                            $calculatedValue = $spentAmount;
                                            $totalPercent = $spentAmount;
                                        }
                                    }
                                }
                            }
                           
                            $refundedTotalValue = '';
                            if (!empty($order->getWalletItemCancel())) {
                                $walletItemCancel = $this->serializer->unserialize($order->getWalletItemCancel());
                                if ($walletItemCancel['refund_money'] < $spentAmount) {
                                    $calculatedValue = $walletItemCancel['refund_money'];
                                    $refundedTotalValue = $calculatedValue + $totalPercent;
                                    if ($refundedTotalValue > $spentAmount) {
                                        $totalPercent = $spentAmount - $calculatedValue;
                                        $calculatedValue = $spentAmount - $calculatedValue;
                                        $refundedTotalValue = $totalPercent + $walletItemCancel['refund_money'];
                                        $logger->info(print_r("greater than spentAmount: ".$totalPercent, true));
                                    }
                                    $logger->info(print_r("refundedTotalValue: ".$refundedTotalValue, true));
                                } else {
                                    $totalPercent = 0;
                                }
                            }
                            if ($isAllItemsCanceled) {
                                $logger->info('----------------ALL ITEM returned----------------');
                                $refunded = empty($refundedTotalValue) ? 0 : floor($refundedTotalValue);
                                $walletBalanceAllItemCancelled = $spentAmount - $refunded;
                                $logger->info(print_r("walletBalanceAllItemCancelled: ".$walletBalanceAllItemCancelled, true));
                                if ($totalPercent > 0) {
                                    if ($spentAmount != $walletBalanceAllItemCancelled) {
                                        $calculatedValue = $totalPercent + $walletBalanceAllItemCancelled;
                                        $totalPercent = $calculatedValue;
                                        $logger->info(print_r("calculated AllItemCancelled: ".$calculatedValue, true));
                                    } else {
                                        $calculatedValue = $spentAmount;
                                        $totalPercent = $calculatedValue;
                                        $logger->info(print_r("calculated AllItemCancelled: ".$calculatedValue, true));
                                    }
                                }
                                $order->getPayment()->cancel();
                                $order->registerCancellation();
                                $order->setState($state)->setStatus($state);

                                // $cancelReason = array('orderid' => $order->getIncrementId());
                                // $templateVariable['cancelDetails'] = $cancelReason;
                                // $templateVariable['email'] = $order->getCustomerEmail();
                                // $templateVariable['template_id'] = 'order/order_cancel_reason_config/cancel_reason_email';
                                // $this->helperData->sendMail($templateVariable);
                                // $cancelVar['order_id'] = $order->getIncrementId();
                                // if (!empty($order->getShippingAddress())){
                                //     $cancelVar['mobileNo'] = $order->getShippingAddress()->getData('telephone');
                                // } else {
                                //     $cancelVar['mobileNo'] = $order->getBillingAddress()->getData('telephone');
                                // }
                                // $cancelVar['templateid'] = 'order/order_cancel_reason_config/cancel_reason_sms';
                                // $this->helperData->sendCancelReasonSms($cancelVar);
                                /*send copy to admin*/
                                // $notifyAdmin['cancelDetails'] = $cancelReason;
                                // $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                                // $email = $this->scopeConfig->getValue('msggateway/sales_order_configuration/copy_of_order', $storeScope);
                                // $email = trim($email);
                                // $email = explode(',', $email);
                                // $notifyAdmin['email'] = $email;
                                // $notifyAdmin['template_id'] = 'order/order_cancel_reason_config/cancel_reason_email';
                                // $this->helperData->sendMail($notifyAdmin);
                            }
                            $comments = 'Customer canceled the item '.$item->getName().' for the reason: '.$comment;
                            $notify = false;
                            $orderCommentSender = $this->OrderCommentSender;
                            $orderCommentSender->send($order, $notify, $comments);
                            $order->addCommentToStatusHistory($comment, false, true)->setIsCustomerNotified(false);
                            $customerSessionData = $this->customerSession->create()->getCustomer();
                            $totalBalance = is_null($customerSessionData->getWalletBalance()) ? 0 : $customerSessionData->getWalletBalance();
                            if ($calculatedValue <= $spentAmount && $spentAmount > 0) {
                                $logger->info(print_r("calculatedValue: ".$calculatedValue, true));
                                $logger->info(print_r("spentAmount: ".$spentAmount, true));
                                $logger->info(print_r("refunded value: ", true));
                                $logger->info(print_r($totalPercent, true));
                                if ($totalPercent > 0) {
                                    $model = $this->walletFactory->create();
                                    $data['comment'] = "Order ".$order->getIncrementId()." Cancellation Refund";
                                    $data['amount'] = $totalPercent;
                                    $data['flag'] = 1;
                                    $data['performed_by'] = "customer";
                                    $data['visibility'] = 1;
                                    $data['customer_id'] = $order->getCustomerId();
                                    $model->setData($data);
                                    $model->save();
                                    $customerModel = $this->customer->load($order->getCustomerId());
                                    $customerData = $customerModel->getDataModel();
                                    $balance = $totalBalance + $model->getAmount();
                                    $customerData->setCustomAttribute('wallet_balance',$balance);
                                    $customerModel->updateData($customerData);
                                    $customerResource = $this->customerFactory->create();
                                    $customerResource->saveAttribute($customerModel, 'wallet_balance');
                                    $walletRefund = [];
                                    $walletRefund['is_refund'] = true;
                                    $walletRefund['refund_money'] = empty($refundedTotalValue) ? $calculatedValue : $refundedTotalValue;
                                    $order->setWalletItemCancel($this->serializer->serialize($walletRefund));
                                    $walletVar['order_id'] = $order->getIncrementId();
                                    $walletVar['amount'] = round($model->getAmount()).' INR';
                                    if (!empty($order->getShippingAddress())){
                                        $walletVar['mobileNo'] = $order->getShippingAddress()->getData('telephone');
                                    } else {
                                        $walletVar['mobileNo'] = $order->getBillingAddress()->getData('telephone');
                                    }
                                    $walletVar['templateid'] = 'order/order_cancel_reason_config/order_cancel_reason_sms';
                                    $this->helperData->sendOrderCancelReturnWalletSms($walletVar);
                                    $cancelReason = array('orderid' => $order->getIncrementId(), 'amount' => round($model->getAmount()));
                                    $templateVariable['cancelDetails'] = $cancelReason;
                                    $templateVariable['email'] = $order->getCustomerEmail();
                                    $templateVariable['template_id'] = 'order/order_cancel_reason_config/order_cancel_reason_email';
                                    $this->helperData->sendMail($templateVariable);
                                }
                            }
                            $order->save();
                            $logger->info(print_r("wallet item cancel: ".$order->getWalletItemCancel(), true));
                            $logger->info(print_r("--------End--------", true));
                            $this->messageManager->addSuccessMessage('Order Item Cancelled successfully');
                        }
                    // } else {
                    //     $this->messageManager->addErrorMessage('Can\'t able to cancel this order item');
                    // }
                } else {
                    $this->messageManager->addErrorMessage('Can\'t able to cancel this order item');
                }
            } else {
                $this->messageManager->addError(__('Something went wrong. Please try again later.'));

            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
            __($e->getMessage())
            );
        }
        $this->_redirect('sales/orders/history/');
        return $resultPage;
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
    public function razororderId($grandTotal, $receipt, $note)
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
            $amount = (int) (number_format($grandTotal * 100, 0, ".", ""));
            $order = $this->razor->rzp->order->create([
                'amount' => $amount,
                'receipt' => $receipt,
                'currency' => 'INR',
                'payment_capture' => $payment_capture,
                'notes' => $note,
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