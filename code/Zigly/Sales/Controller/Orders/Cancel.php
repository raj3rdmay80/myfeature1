<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
namespace Zigly\Sales\Controller\Orders;

use Zigly\Sales\Helper\Data;
use Magento\Sales\Model\Order;
use Magento\Customer\Model\Customer;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

class Cancel extends \Magento\Framework\App\Action\Action
{

    /**
     * @var PageFactory
    */
    protected $pageFactory;

    /**
     * @param Order $order
     * @param Data $helperData
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param PageFactory $resultPageFactory
     * @param Customer $customer
     * @param WalletFactory $walletFactory
     * @param CustomerFactory $customerFactory
     * @param OrderCommentSender $OrderCommentSender
     * @param ScopeConfigInterface $scopeConfig
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
        ScopeConfigInterface $scopeConfig,
        OrderCommentSender $OrderCommentSender,
        OrderManagementInterface $orderManagement,
        OrderItemRepositoryInterface $itemsRepository
    ) {
        $this->order = $order;
        $this->helperData = $helperData;
        $this->customer = $customer;
        $this->serializer = $serializer;
        $this->walletFactory = $walletFactory;
        $this->customerFactory = $customerFactory;
        $this->scopeConfig = $scopeConfig;
        $this->pageFactory = $pageFactory;
        $this->orderManagement = $orderManagement;
        $this->itemsRepository = $itemsRepository;
        $this->OrderCommentSender = $OrderCommentSender;
        $this->resultJsonFactory = $resultJsonFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $result = $this->resultJsonFactory->create();
        $post = $this->getRequest()->getPostValue();
        /*$orderId = $this->getRequest()->getParam('id');*/
        $orderId = $post['orderid'];
        $comment = $post['cancelreason'];
        $orders = $this->order->load($orderId);
        $state = 'canceled';
        $status = 'cancelled';
        try {
            $status = ['processing', 'pending'];
            if (in_array($orders->getStatus(), $status)) {
                $orders->getPayment()->cancel();
                $orders->registerCancellation();
                $orders->setState($state)->setStatus($state);
                if (!empty($orders->getZwallet())){
                    $zWallet = $this->serializer->unserialize($orders->getZwallet());
                    if ($zWallet['applied'] == true) {
                        $total = $zWallet['spend_amount'];
                        $model = $this->walletFactory->create();
                        $data['comment'] = "Order ".$orders->getIncrementId()." Cancellation Refund";
                        $data['amount'] = $total;
                        $data['flag'] = 1;
                        $data['performed_by'] = "customer";
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
                $comments = 'Order is cancelled by Customer'."\n".$comment;
                $notify = false;
                $orderCommentSender = $this->OrderCommentSender;
                $orderCommentSender->send($orders, $notify, $comments);
                $orders->addCommentToStatusHistory($comment, false, true)->setIsCustomerNotified(false);
                $cancelReason = array('orderid' => $orders->getIncrementId());
                $templateVariable['cancelDetails'] = $cancelReason;
                $templateVariable['email'] = $orders->getCustomerEmail();
                $templateVariable['template_id'] = 'order/order_cancel_reason_config/cancel_reason_email';
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
                $notifyAdmin['template_id'] = 'order/order_cancel_reason_config/cancel_reason_email';
                $this->helperData->sendMail($notifyAdmin);
                $orders->save();
                $this->messageManager->addSuccessMessage('Order Cancelled successfully');
            } else {
                $this->messageManager->addErrorMessage('Can\'t able to cancel this order');
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
            __($e->getMessage())
            );
        }
        $this->_redirect('sales/orders/history/');
        return $resultPage;
    }
}