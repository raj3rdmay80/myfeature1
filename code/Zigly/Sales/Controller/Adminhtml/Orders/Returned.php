<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Zigly\Sales\Controller\Adminhtml\Orders;

use Zigly\Sales\Helper\Data;
use Magento\Sales\Model\Order;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\Customer;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

class Returned extends \Zigly\Sales\Controller\Adminhtml\Orders\Order
{

    protected $request;

    protected $messageManager;

    /**
     * @param Order $order
     * @param Data $helperData
     * @param Context $context
     * @param RequestInterface $request
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param Customer $customer
     * @param WalletFactory $walletFactory
     * @param CustomerFactory $customerFactory
     * @param SerializerInterface $serializer
     * @param ScopeConfigInterface $scopeConfig
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param ManagerInterface $messageManager
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
        $this->scopeConfig = $scopeConfig;
        $this->customer = $customer;
        $this->serializer = $serializer;
        $this->walletFactory = $walletFactory;
        $this->customerFactory = $customerFactory;
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
     * return order
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $result = $this->resultJsonFactory->create();
        $post = $this->getRequest()->getPostValue();
        $orderId = $post['orderid'];
        $comment = $post['return_reason'];
        $orders = $this->order->load($orderId);
        $state = 'returned';
        $status = 'returned';
        if ($orders) {
            try {
                $status = ['complete'];
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
                                $data['comment'] = "Order ".$orders->getIncrementId()." Return Refund";
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
                    $comments = 'Order is returned by Admin'."\n".$comment;
                    $notify = false;
                    $orderCommentSender = $this->OrderCommentSender;
                    $orderCommentSender->send($orders, $notify, $comments);
                    $orders->addCommentToStatusHistory($comments, false, true)->setIsCustomerNotified(false);
                    $returnReason = array('orderid' => $orders->getIncrementId(), 'days' => '7');
                    $templateVariable['cancelDetails'] = $returnReason;
                    $templateVariable['email'] = $orders->getCustomerEmail();
                    $templateVariable['template_id'] = 'order/order_cancel_reason_config/return_reason_admin_email';
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
                    $notifyAdmin['template_id'] = 'order/order_cancel_reason_config/return_reason_admin_email';
                    $this->helperData->sendMail($notifyAdmin);
                    $orders->save();
                    $this->messageManager->addSuccessMessage('Order Returned successfully');
                } else {
                    $this->messageManager->addErrorMessage('Can\'t able to return this order');
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('You have not returned the item.'));
            }
        }
        return $resultPage;
    }
}