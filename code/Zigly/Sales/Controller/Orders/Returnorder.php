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
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

class Returnorder extends \Magento\Framework\App\Action\Action
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
     * @param Order $order
     * @param Data $helperData
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Customer $customer
     * @param WalletFactory $walletFactory
     * @param CustomerFactory $customerFactory
     * @param SerializerInterface $serializer
     * @param ScopeConfigInterface $scopeConfig
     * @param PageFactory $resultPageFactory
     * @param OrderCommentSender $OrderCommentSender
     * @param OrderRepositoryInterface $orderRepository
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
        OrderRepositoryInterface $orderRepository,
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
        $this->resultJsonFactory = $resultJsonFactory;
        $this->OrderCommentSender = $OrderCommentSender;
        return parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $result = $this->resultJsonFactory->create();
        try {
            $post = $this->getRequest()->getPostValue();
            $orderId = $post['orderid'];
            $comment = $post['return_reason'];
            $orders = $this->order->load($orderId);
            $state = 'returned';
            $status = 'returned';
            if ($orders->getStatus() == "complete")
            {
                $orders->getPayment()->cancel();
                $orders->registerCancellation();
                $orders->setState($state)->setStatus($state);
                if (!empty($orders->getZwallet())){
                    $zWallet = $this->serializer->unserialize($orders->getZwallet());
                    if ($zWallet['applied'] == true) {
                        $total = $zWallet['spend_amount'];
                        $model = $this->walletFactory->create();
                        $data['comment'] = "Order ".$orders->getIncrementId()." Return Refund";
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
                $comments = 'Order is returned by Customer'."\n".$comment;
                $notify = false;
                $orderCommentSender = $this->OrderCommentSender;
                $orderCommentSender->send($orders, $notify, $comments);
                $orders->addCommentToStatusHistory($comment, false, true)->setIsCustomerNotified(false);
                $returnReason = array('orderid' => $orders->getIncrementId(), 'days' => '7');
                $templateVariable['cancelDetails'] = $returnReason;
                $templateVariable['email'] = $orders->getCustomerEmail();
                $templateVariable['template_id'] = 'order/order_cancel_reason_config/return_reason_email';
                $this->helperData->sendMail($templateVariable);
                $returnVar['order_id'] = $orders->getIncrementId();
                $returnVar['days'] = '7';
                if (!empty($orders->getShippingAddress())){
                    $returnVar['mobileNo'] = $orders->getShippingAddress()->getData('telephone');
                } else {
                    $returnVar['mobileNo'] = $orders->getBillingAddress()->getData('telephone');
                }
                $returnVar['templateid'] = 'order/order_cancel_reason_config/return_reason_sms';
                $this->helperData->sendReturnReasonSms($returnVar);
                /*send copy to admin*/
                $notifyAdmin['cancelDetails'] = $returnReason;
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $email = $this->scopeConfig->getValue('msggateway/sales_orders_configuration/copy_of_order', $storeScope);
                $email = trim($email);
                $email = explode(',', $email);
                $notifyAdmin['email'] = $email;
                $notifyAdmin['template_id'] = 'order/order_cancel_reason_config/return_reason_email';
                $this->helperData->sendMail($notifyAdmin);
                $orders->save();
                $this->messageManager->addSuccess(__('You returned this order successfully.'));
            } else {
                $this->messageManager->addError(__('Can\'t able to return this order.'));
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