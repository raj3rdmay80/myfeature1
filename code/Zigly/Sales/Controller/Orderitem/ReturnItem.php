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

class ReturnItem extends \Magento\Framework\App\Action\Action
{

    /**
     * @var PageFactory
    */
    protected $pageFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

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
     * @param Customer $customer
     * @param WalletFactory $walletFactory
     * @param CustomerFactory $customerFactory
     * @param SerializerInterface $serializer
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerSessionFactory $customerSession
     * @param PageFactory $resultPageFactory
     * @param OrderCommentSender $OrderCommentSender
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderItemRepositoryInterface $itemToBeReturnedsRepository
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
        CustomerSessionFactory $customerSession,
        JsonFactory $resultJsonFactory,
        ScopeConfigInterface $scopeConfig,
        OrderCommentSender $OrderCommentSender,
        OrderRepositoryInterface $orderRepository,
        Razor $razor,
        Config $config,
        OrderItemRepositoryInterface $itemToBeReturnedsRepository
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
        $this->itemsRepository = $itemToBeReturnedsRepository;
        $this->customerSession = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->razor = $razor;
        $this->config = $config;
        $this->OrderCommentSender = $OrderCommentSender;
        return parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $result = $this->resultJsonFactory->create();
        $now = new \DateTime('now');
        $currentTimeStamp = $now->getTimestamp() + $now->getOffset();
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/ReturnItem-'.$now->format('d-m-Y').'.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('------------------START-----------------');
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        try {
            $post = $this->getRequest()->getPostValue();
            $customerId = $this->customerSession->create()->getCustomerId();
            if (!empty($post['orderid']) && !empty($post['return_reason']) && !empty($post['itemid'])) {
                $orderId = $post['orderid'];
                $comment = $post['return_reason'];
                $logger->debug(var_export($post, true));
                $itemToBeReturned = $this->itemsRepository->get($post['itemid']);
                $order = $this->order->load($orderId);
                $logger->info('------------Itemloaded--------------');

                if ($order && $order->getStatus() == "complete" &&
                    $order->getCustomerId() == $customerId &&
                    $itemToBeReturned && $itemToBeReturned->getOrderId() == $orderId &&
                    (int)$itemToBeReturned->getQtyCanceled() <= 0 && (int)$itemToBeReturned->getQtyReturned() <= 0 
                    && !empty($itemToBeReturned->getReturnPolicyDate())
                ) {

                    $logger->info('----------------Partially validated----------------');
                    $returnPolicyEndDate = new \DateTime($itemToBeReturned->getReturnPolicyDate());
                    if ($returnPolicyEndDate > $now) {
                        $logger->info('----------------Can Return----------------');
                        $isAllItemsReturned = 1;
                        $calculatedItemAmount = 0;
                        foreach ($order->getAllItems() as $orderItem) {
                            if (!empty($orderItem->getParentItemId())) continue;
                            if ($orderItem->getItemId() == $post['itemid']) {
                                $loopedItem = $orderItem;
                                continue;
                            }
                            if ((int)$orderItem->getQtyOrdered() != (int)$orderItem->getQtyReturned() && (int)$orderItem->getQtyOrdered() != (int)$orderItem->getQtyCanceled()) {
                                $isAllItemsReturned = 0;
                                $logger->info('itemEntityId'.$orderItem->getItemId());
                                $logger->info('itemRowTotal'.$orderItem->getRowTotal());
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
                        if ((float)$order->getShippingAmount() <= 0 && $calculatedItemAmount <= 499 && !empty($shipamount) && !$isAllItemsReturned) {
                            // if (false) {
                            // if ($calculatedItemAmount <= 499) {
                            $logger->info(print_r("--------New FLow Capture Shipping--------", true));
                            $logger->info(print_r("Shipping-Amount".$shipamount, true));
                            $note = [
                                'type' => 'return',
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
                            $itemToBeReturned->setRazorpayOrderId($responseData['razorData']['razorId'])->save(); //$qty
                            $result->setData($responseData);
                            $logger->info(print_r("--------New FLow END--------", true));
                            return $result;
                        } else {
                            $logger->info('----------------Can Return - OLD FLOW---------------');
                            $loopedItem->setQtyReturned((int)$loopedItem->getQtyOrdered())->save();
                            $logger->debug(var_export($itemToBeReturned->getData(), true));
                             //$qty
                            $logger->info('----------------Saved---------------');
                            $calculatedValue = 1;
                            $totalPercent = 0;
                            $spentAmount = 0;
                            if (!empty($order->getZwallet())){
                                $zWallet = $this->serializer->unserialize($order->getZwallet());
                                if ($zWallet['applied'] == true) {
                                    $maxTransactionPercent = (float)$zWallet['percent'];
                                    $spentAmount = floor($zWallet['spend_amount']);
                                    $rowTotalPercent = floor(($maxTransactionPercent / 100) * $itemToBeReturned->getRowTotal());
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
                            $comments = 'Customer returned the item '.$itemToBeReturned->getName().' for the reason: '.$comment;
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

                        }
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
                    } else {
                        $this->messageManager->addErrorMessage('Can\'t able to cancel this order item');
                    }
                } else {
                    $logger->info('Can\'t able to return this order item');
                    $this->messageManager->addError(__('Can\'t able to return this order item.'));
                }
            } else {
                $logger->info('Post Data Missing');
                $this->messageManager->addError(__('Something went wrong. Please try again later.'));
            }
        } catch (\Exception $e) {
            $logger->info('------Exception:'.$e->getMessage());
            $this->messageManager->addError(__('Something went wrong. Please try again later.'));
            // throw new \Magento\Framework\Exception\LocalizedException(
            // __($e->getMessage())
            // );
        }
        $logger->info('----------------END---------------');
        // $this->_redirect('sales/orders/history/');
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