<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
declare(strict_types=1);

namespace Zigly\Sales\Controller\Adminhtml\Orders;

use Zigly\Sales\Helper\Data;
use Magento\Sales\Model\Order;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Customer\Model\Customer;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

class Cancel extends \Zigly\Sales\Controller\Adminhtml\Orders\Order
{

    protected $request;

    protected $messageManager;

    /**
     * @param Order $order
     * @param Data $helperData
     * @param Context $context
     * @param RequestInterface $request
     * @param PageFactory $resultPageFactory
     * @param Customer $customer
     * @param WalletFactory $walletFactory
     * @param CustomerFactory $customerFactory
     * @param SerializerInterface $serializer
     * @param JsonFactory $resultJsonFactory
     * @param ManagerInterface $messageManager
     * @param ScopeConfigInterface $scopeConfig
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param OrderCommentSender $OrderCommentSender
     * @param OrderItemRepositoryInterface $itemsRepository
     */
    public function __construct(
        Order $order,
        Data $helperData,
        Context $context,
        Customer $customer,
        WalletFactory $walletFactory,
        SerializerInterface $serializer,
        CustomerFactory $customerFactory,
        RequestInterface $request,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        ManagerInterface $messageManager,
        ScopeConfigInterface $scopeConfig,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        OrderCommentSender $OrderCommentSender,
        OrderItemRepositoryInterface $itemsRepository
    ) {
        $this->order = $order;
        $this->request = $request;
        $this->helperData = $helperData;
        $this->customer = $customer;
        $this->serializer = $serializer;
        $this->walletFactory = $walletFactory;
        $this->customerFactory = $customerFactory;
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
        $this->itemsRepository = $itemsRepository;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->OrderCommentSender = $OrderCommentSender;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $result = $this->resultJsonFactory->create();
        $post = $this->getRequest()->getPostValue();
        $orderId = $post['orderid'];
        $comment = $post['cancelreason'];
        $orders = $this->order->load($orderId);
        $state = 'canceled';
        $status = 'cancelled';
        if ($orders) {
            try {
                $status = ['processing', 'pending'];
                if (in_array($orders->getStatus(), $status)) {
                    $orders->getPayment()->cancel();
                    $orders->registerCancellation();
                    $orders->setState($state)->setStatus($state);
                    if (!empty($orders->getZwallet())){
                        $zWallet = $this->serializer->unserialize($orders->getZwallet());
                        if ($zWallet['applied'] == true) {
                            if (!empty($orders->getWalletItemCancel())) {
                                $walletItemCancel = $this->serializer->unserialize($orders->getWalletItemCancel());
                                if ($walletItemCancel['refund_money'] <= $zWallet['spend_amount']) {
                                    $total = $zWallet['spend_amount'] - $walletItemCancel['refund_money'];
                                }
                            } else {
                                $total = $zWallet['spend_amount'];
                            }
                            if ($total > 0) {
                                $model = $this->walletFactory->create();
                                $data['comment'] = "Order ".$orders->getIncrementId()." Cancellation Refund";
                                $data['amount'] = $total;
                                $data['flag'] = 1;
                                $data['performed_by'] = "admin";
                                $data['visibility'] = 1;
                                $data['customer_id'] = $orders->getCustomerId();
                                $model->setData($data);
                                $model->save();
                                $customerModel = $this->customer->load($orders->getCustomerId());
                                $customerData = $customerModel->getDataModel();
                                $totalBalance = is_null($customerModel->getWalletBalance()) ? "0" : $customerModel->getWalletBalance();
                                $balance = $totalBalance + $total;
                                $customerData->setCustomAttribute('wallet_balance',$balance);
                                $customerModel->updateData($customerData);
                                $customerResource = $this->customerFactory->create();
                                $customerResource->saveAttribute($customerModel, 'wallet_balance');
                            }
                        }
                    }
                    $itemcancelled = [];
                    $orderItems = $orders->getAllItems();
                    foreach ($orderItems as $items) {
                        $id = $items->getId();
                        $item = $this->itemsRepository->get($id);
                        $qty = $item->getQtyOrdered();
                        $item->setQtyCanceled($qty)->save();
                        $itemcancelled[] = $item;
                    }
                    $orders->setItems($itemcancelled);
                    $comments = 'Order is cancelled by Admin'."\n".$comment;
                    $notify = false;
                    $orderCommentSender = $this->OrderCommentSender;
                    $orderCommentSender->send($orders, $notify, $comments);
                    $orders->addCommentToStatusHistory($comments, false, true)->setIsCustomerNotified(false);
                    $cancelReason = array('orderid' => $orders->getIncrementId());
                    $templateVariable['cancelDetails'] = $cancelReason;
                    $templateVariable['email'] = $orders->getCustomerEmail();
                    $templateVariable['template_id'] = 'order/order_cancel_reason_config/cancel_reason_admin_email';
                    $this->helperData->sendMail($templateVariable);
                    $cancelVar['order_id'] = $orders->getIncrementId();
                    if (!empty($orders->getShippingAddress())){
                        $cancelVar['mobileNo'] = $orders->getShippingAddress()->getData('telephone');
                    } else {
                        $cancelVar['mobileNo'] = $orders->getBillingAddress()->getData('telephone');
                    }
                    $cancelVar['templateid'] = 'order/order_cancel_reason_config/cancel_reason_sms';
                    $this->helperData->sendCancelReasonSms($cancelVar);
                    /*send copy to admin*/
                    $notifyAdmin['cancelDetails'] = $cancelReason;
                    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                    $email = $this->scopeConfig->getValue('msggateway/sales_orders_configuration/copy_of_order', $storeScope);
                    $email = trim($email);
                    $email = explode(',', $email);
                    $notifyAdmin['email'] = $email;
                    $notifyAdmin['template_id'] = 'order/order_cancel_reason_config/cancel_reason_admin_email';
                    $this->helperData->sendMail($notifyAdmin);
                    $orders->save();
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {}
        }
        return $resultPage;
    }
}