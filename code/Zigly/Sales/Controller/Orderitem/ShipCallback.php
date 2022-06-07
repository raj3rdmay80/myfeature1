<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
declare(strict_types=1);

namespace Zigly\Sales\Controller\Orderitem;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Zigly\Sales\Helper\Data;
use Magento\Sales\Model\Order;
use Magento\Customer\Model\Customer;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Customer\Model\SessionFactory as CustomerSessionFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;
use Razorpay\Magento\Model\Config;
use Razorpay\Magento\Controller\Payment\Order as Razor;

/**
 * Razor pay callback Shipping amount validated
 */
class ShipCallback extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
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
     * Customer session model
     *
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var PageFactory
    */
    protected $pageFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;


    /**
     * @param Order $order
     * @param Data $helperData
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Customer $customer
     * @param WalletFactory $walletFactory
     * @param CustomerFactory $customerFactory
     * @param SerializerInterface $serializer
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerSessionFactory $customerSession
     * @param PageFactory $resultPageFactory
     * @param OrderCommentSender $OrderCommentSender
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderItemRepositoryInterface $itemsRepository
     */
    public function __construct(
        Context $context,
        Order $order,
        Data $helperData,
        PageFactory $pageFactory,
        Customer $customer,
        WalletFactory $walletFactory,
        SerializerInterface $serializer,
        CustomerFactory $customerFactory,
        CustomerSessionFactory $customerSession,
        JsonFactory $resultJsonFactory,
        ScopeConfigInterface $scopeConfig,
        OrderCommentSender $OrderCommentSender,
        OrderRepositoryInterface $orderRepository,
        Razor $razor,
        Config $config,
        OrderItemRepositoryInterface $itemsRepository
    ) {
        $this->order = $order;
        $this->helperData = $helperData;
        $this->pageFactory = $pageFactory;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
        $this->customer = $customer;
        $this->serializer = $serializer;
        $this->walletFactory = $walletFactory;
        $this->customerFactory = $customerFactory;
        $this->itemsRepository = $itemsRepository;
        $this->customerSession = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->razor = $razor;
        $this->config = $config;
        $this->OrderCommentSender = $OrderCommentSender;
        parent::__construct($context);
    }

    public function execute()
    {
        $now = new \DateTime('now');
        $currentTimeStamp = $now->getTimestamp() + $now->getOffset();
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/shipCallback-'.$now->format('d-m-Y').'.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('-----------------------(Ship...Online)--------------------------------');

        try {
            $razorconfig = $this->config;
            $postData = $this->getRequest()->getPostValue();
            $logger->debug(var_export($postData, true));
            // echo ("<pre>");
            // print_r($postData);
            if(!empty($postData['razorpay_payment_id'])) {
                $logger->info('-----------------------(Transaction ID)--------------------------------');
                $transactionId = $postData['razorpay_payment_id'];
                $logger->debug(var_export($transactionId, true));
                $secret = $razorconfig->getConfigData('key_secret');
                // $orderId = $groomSession['booking_razor_id'];
                $razororder = $this->razor->rzp->order->fetch($postData['razorpay_order_id']); // ->payments()
                $itemId = $razororder->notes->itemid;
                if (!empty($itemId)) {
                    $actionType = $razororder->notes->type;
                    $comment = $razororder->notes->comment;
                    $item = $this->itemsRepository->get($itemId);
                    $order = $this->order->load($razororder->notes->orderid);
                    $orderId = $item->getRazorpayOrderId();
                        // $shippingCaptured = $razororder->amount_paid/100;

                    // $comments = 'Actual Amount Paid of '.$order->formatBasePrice($shippingCaptured).', with Razorpay for Shipping.';

                    //                             $order->addCommentToStatusHistory($comments, false, true)->setIsCustomerNotified(false);
                    $payload = $orderId . '|' . $postData['razorpay_payment_id'];
                    $expectedSignature = hash_hmac('sha256', $payload, $secret);
                    if ($expectedSignature == $postData['razorpay_signature']) {
                        $logger->info('-----------------------(MATCHED)--------------------------------');
                        $shippingCaptured = $razororder->amount_paid/100;
                        $comments = 'Actual Amount Paid of '.$order->formatBasePrice($shippingCaptured).', with Razorpay for Shipping.';
                        if ($actionType == 'return' ) {
                            $this->returnItem($item, $order, $logger, $comment);
                            $this->updateOrder($order, $comments, $shippingCaptured);
                        } else if ($actionType == 'cancel' ) {
                            $this->cancelItem($item, $order, $logger, $comment);
                            $this->updateOrder($order, $comments, $shippingCaptured);
                            // $order->addCommentToStatusHistory($comments, false, true)
                            //     ->setIsCustomerNotified(false);
                            // $order->setBaseShippingAmount($shippingCaptured)
                            //     ->setShippingAmount($shippingCaptured)
                            //     ->setShippingInclTax($shippingCaptured)
                            //     ->setBaseShippingInclTax($shippingCaptured)
                            //     ->save();
                        }
                        $logger->info('-----------------------(successful)--------------------------------');
                        return $this->_redirect('sales/orders/view/order_id/'.$order->getEntityId());
                    }
                }
            }
        } catch (\Exception $e) {
            $logger->info('-----------------------Exception-------------------------------');
            $logger->debug(var_export($e->getMessage(), true));
        }
        $this->messageManager->addWarning( __('Something went wrong. Please try again') );
        return $this->_redirect('sales/orders/history');
    }

    public function returnItem($item, $order, $logger, $comment)
    {
        $logger->info('----------------Can Return----------------');
        $item->setQtyReturned((int)$item->getQtyOrdered())->save(); //$qty
        $orderItems = $order->getAllItems();
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
        $isAllItemsReturned = 1;
        foreach ($orderItems as $orderItem) {
            if (!empty($orderItem->getParentItemId())) continue;
            if ((int)$orderItem->getQtyOrdered() != (int)$orderItem->getQtyReturned() && (int)$orderItem->getQtyOrdered() != (int)$orderItem->getQtyCanceled()) {
                $isAllItemsReturned = 0;
                break;
            }
        }
        $refundedTotalValue = '';
        if (!empty($order->getWalletItemCancel())) {
            $walletItemReturn = $this->serializer->unserialize($order->getWalletItemCancel());
            if ($walletItemReturn['refund_money'] < $spentAmount) {
                $calculatedValue = $walletItemReturn['refund_money'];
                $refundedTotalValue = $calculatedValue + $totalPercent;
                if ($refundedTotalValue > $spentAmount) {
                    $totalPercent = $spentAmount - $calculatedValue;
                    $calculatedValue = $spentAmount - $calculatedValue;
                    $refundedTotalValue = $totalPercent + $walletItemReturn['refund_money'];
                    $logger->info(print_r("greater than spentAmount: ".$totalPercent, true));
                }
                $logger->info(print_r("refundedTotalValue: ".$refundedTotalValue, true));
            } else {
                $totalPercent = 0;
            }
        }
        if ($isAllItemsReturned) {
            $logger->info('----------------ALL ITEM returned----------------');
            $refunded = empty($refundedTotalValue) ? 0 : floor($refundedTotalValue);
            $walletBalanceAllItemReturned = $spentAmount - $refunded;
            $logger->info(print_r("walletBalanceAllItemReturned: ".$walletBalanceAllItemReturned, true));
            if ($totalPercent > 0) {
                if ($spentAmount != $walletBalanceAllItemReturned) {
                    $calculatedValue = $totalPercent + $walletBalanceAllItemReturned;
                    $totalPercent = $calculatedValue;
                    $logger->info(print_r("calculated AllItemReturned: ".$calculatedValue, true));
                } else {
                    $calculatedValue = $spentAmount;
                    $totalPercent = $calculatedValue;
                    $logger->info(print_r("calculated AllItemReturned: ".$calculatedValue, true));
                }
            }
            $state = 'returned';
            $status = 'returned';
            $order->getPayment()->cancel();
            $order->registerCancellation();
            $order->setState($state)->setStatus($state);
        }
        $comments = 'Customer returned the item '.$item->getName().' for the reason: '.$comment;
        $notify = false;
        $orderCommentSender = $this->OrderCommentSender;
        $orderCommentSender->send($order, $notify, $comments);
        $order->addCommentToStatusHistory($comments, false, true)->setIsCustomerNotified(false);
        $customerSessionData = $this->customerSession->create()->getCustomer();
        $totalBalance = is_null($customerSessionData->getWalletBalance()) ? 0 : $customerSessionData->getWalletBalance();
        if ($calculatedValue <= $spentAmount && $spentAmount > 0) {
            $logger->info(print_r("calculatedValue: ".$calculatedValue, true));
            $logger->info(print_r("spentAmount: ".$spentAmount, true));
            $logger->info(print_r("refunded value: ", true));
            $logger->info(print_r($totalPercent, true));
            if ($totalPercent > 0) {
                $model = $this->walletFactory->create();
                $data['comment'] = "Order ".$order->getIncrementId()." Return Refund";
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
                $returnReason = array('orderid' => $order->getIncrementId(), 'amount' => round($model->getAmount()));
                $templateVariable['cancelDetails'] = $returnReason;
                $templateVariable['email'] = $order->getCustomerEmail();
                $templateVariable['template_id'] = 'order/order_cancel_reason_config/order_cancel_reason_email';
                $this->helperData->sendMail($templateVariable);
            }
        }
        $order->save();
        $logger->info(print_r("wallet item return: ".$order->getWalletItemCancel(), true));
        $logger->info(print_r("--------End--------", true));
        // $returnReason = array('orderid' => $order->getIncrementId(), 'days' => '7');
        // $templateVariable['cancelDetails'] = $returnReason;
        // $templateVariable['email'] = $order->getCustomerEmail();
        // $templateVariable['template_id'] = 'order/order_cancel_reason_config/return_reason_email';
        // $this->helperData->sendMail($templateVariable);
        // $returnVar['order_id'] = $order->getIncrementId();
        // $returnVar['days'] = '7';
        // if (!empty($order->getShippingAddress())){
        //     $returnVar['mobileNo'] = $order->getShippingAddress()->getData('telephone');
        // } else {
        //     $returnVar['mobileNo'] = $order->getBillingAddress()->getData('telephone');
        // }
        // $returnVar['templateid'] = 'order/order_cancel_reason_config/return_reason_sms';
        // $this->helperData->sendReturnReasonSms($returnVar);
        // /*send copy to admin*/
        // $notifyAdmin['cancelDetails'] = $returnReason;
        // $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        // $email = $this->scopeConfig->getValue('msggateway/sales_order_configuration/copy_of_order', $storeScope);
        // $email = trim($email);
        // $email = explode(',', $email);
        // $notifyAdmin['email'] = $email;
        // $notifyAdmin['template_id'] = 'order/order_cancel_reason_config/return_reason_email';
        // $this->helperData->sendMail($notifyAdmin);
        $logger->info('----------------Success END---------------');
        $this->messageManager->addSuccess(__('You returned this order item successfully.'));
    }

    public function cancelItem($item, $order, $logger, $comment)
    {
        $logger->info('----------------WIthin policy date----------------');
        $item->setQtyCanceled((int)$item->getQtyOrdered())->save(); //$qty
        $orderItems = $order->getAllItems();
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
        $isAllItemsCanceled = 1;
        foreach ($orderItems as $orderItem) {
            if (!empty($orderItem->getParentItemId())) continue;
            if ((int)$orderItem->getQtyOrdered() != (int)$orderItem->getQtyCanceled()) {
                $logger->info('----------------Cannot Cancel whole order----------------');
                $isAllItemsCanceled = 0;
                break;
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

    /**
     * Update Order Total with Capture Shipping Amount
     */
    public function updateOrder($order, $comments, $shippingCaptured)
    {
        $order->addCommentToStatusHistory($comments, false, true)
            ->setIsCustomerNotified(false);
        $order->setBaseShippingAmount($shippingCaptured)
            ->setShippingAmount($shippingCaptured)
            ->setShippingInclTax($shippingCaptured)
            ->setBaseShippingInclTax($shippingCaptured);
        if($order->hasInvoices()){
            if ((float)$order->getTotalInvoiced() > 0) {
                $order->setTotalInvoiced((float)$order->getTotalInvoiced() + $shippingCaptured);
            }
            if ((float)$order->getBaseTotalInvoiced() > 0) {
                $order->setBaseTotalInvoiced((float)$order->getBaseTotalInvoiced() + $shippingCaptured);
            }
            if ((float)$order->getBaseTotalPaid() > 0) {
                $order->setBaseTotalPaid((float)$order->getBaseTotalPaid() + $shippingCaptured);
            }
            if ((float)$order->getTotalPaid() > 0) {
                $order->setTotalPaid((float)$order->getTotalPaid() + $shippingCaptured);
            }
            if ((float)$order->getShippingInvoiced() > 0) {
                $order->setShippingInvoiced((float)$order->getShippingInvoiced() + $shippingCaptured);
            }
        } else if ((float)$order->setTotalDue() > 0) {
            $order->setTotalDue((float)$order->getTotalDue() + $shippingCaptured);
            $order->setBaseTotalDue((float)$order->getBaseTotalDue() + $shippingCaptured);
        }
        // shipping_canceled
        // base_shipping_canceled
        // shipping_refunded
        // base_shipping_refunded  ''
        // base_total_due
        // total_due
        $order->setGrandTotal((float)$order->getGrandTotal() + $shippingCaptured)
            ->setBaseGrandTotal((float)$order->getBaseGrandTotal() + $shippingCaptured);
        $order->save();
    }
}
