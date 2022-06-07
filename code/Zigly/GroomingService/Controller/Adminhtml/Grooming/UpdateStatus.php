<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Controller\Adminhtml\Grooming;

use Magento\Framework\Url;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Json\Helper\Data;
use Zigly\Sales\Helper\Data as HelperData;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\Groomer\Model\GroomerFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Zigly\GroomingService\Model\Comment as ServiceComment;
use Zigly\GroomingService\Helper\ServiceStatus;
use Zigly\GroomingService\Helper\NotifyEmail as NotifyEmailHelper;
use Magento\Customer\Model\ResourceModel\CustomerFactory as CustomerResourceFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderFactory;

class UpdateStatus extends \Magento\Backend\App\Action
{

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    protected $dataPersistor;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /** @var ServiceStatus */
    protected $serviceStatus;

    /**
     * @param Context $context
     * @param Data $jsonHelper
     * @param Url $urlHelper
     * @param HelperData $helperData
     * @param AdminSession $authSession
     * @param CustomerFactory $customerFactory
     * @param UrlInterface $urlInterface
     * @param WalletFactory $walletFactory
     * @param JsonFactory $jsonResultFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param NotifyEmailHelper $notifyEmailHelper
     * @param ServiceStatus $serviceStatus
     * @param GroomerFactory $groomerFactory
     * @param DataPersistorInterface $dataPersistor
     * @param CustomerResourceFactory $customerResourceFactory
     */
    public function __construct(
        Data $jsonHelper,
        Context $context,
        HelperData $helperData,
        AdminSession $authSession,
        WalletFactory $walletFactory,
        CustomerFactory $customerFactory,
        StoreManagerInterface $storeManager,
        JsonFactory $jsonResultFactory,
        ScopeConfigInterface $scopeConfig,
        NotifyEmailHelper $notifyEmailHelper,
        ServiceStatus $serviceStatus,
        GroomerFactory $groomerFactory,
        UrlInterface $urlInterface,
        CustomerResourceFactory $customerResourceFactory,
        DataPersistorInterface $dataPersistor,
        PageFactory $resultPageFactory,
        Url $urlHelper,
        OrderFactory $orderFactory,
        Registry $registry
    ) {
        $this->helperData = $helperData;
        $this->jsonHelper = $jsonHelper;
        $this->authSession = $authSession;
        $this->storeManager = $storeManager;
        $this->groomerFactory = $groomerFactory;
        $this->scopeConfig = $scopeConfig;
        $this->dataPersistor = $dataPersistor;
        $this->urlHelper = $urlHelper;
        $this->walletFactory = $walletFactory;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->customerFactory = $customerFactory;
        $this->urlInterface =$urlInterface;
        $this->serviceStatus = $serviceStatus;
        $this->notifyEmailHelper = $notifyEmailHelper;
        $this->customerResourceFactory = $customerResourceFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->orderFactory = $orderFactory;
        $this->registry = $registry;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_GroomingService::Grooming_status_update');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $result = $this->jsonResultFactory->create();
            if ($data) {
                $id = $this->getRequest()->getParam('entity_id');
                $current_user = $this->authSession->getUser();
                $model = $this->_objectManager->create(\Zigly\GroomingService\Model\Grooming::class)->load($id);
                $comment = $this->_objectManager->create(ServiceComment::class);
                if (!$model->getEntityId() && $id) {
                    $this->messageManager->addErrorMessage(__('This Grooming no longer exists.'));
                    return $result;
                }
                $old_service_status = $model->getBookingStatus();
                $cancellableStatus = $this->serviceStatus->getCancellableStatus();
                $cancelStatus = $this->serviceStatus->getCancelStatus();

                $completedStatus = ['Completed'];
                $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                $currentTimeStamp = $now->getTimestamp() + $now->getOffset();
                $customer = $this->customerFactory->create()->load($model->getCustomerId());
                if (in_array($data['booking_status'], $completedStatus)) {
                    $model->setPaymentStatus("Paid");
                    if ($currentTimeStamp < $model->getScheduledTimestamp()) {
                        $this->messageManager->addErrorMessage(__('You are not allowed to complete this booking.'));
                        return $result;
                    }
                    if ($model->getBookingType() == 1) {
                        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                        $bitly = $this->scopeConfig->getValue('groomingservice/feedback/feedback_bitly_url', $storeScope);
                        $serviceVar['url'] = !empty($bitly) ? $bitly : '';
                        $serviceVar['mobileNo'] = $customer->getPhoneNumber();
                        $this->notifyEmailHelper->sendFeedbackServiceSms($serviceVar);
                    }
                }

                $now->add(new \DateInterval('PT30M'));
                $currentTimeStamp = $now->getTimestamp() + $now->getOffset();
                if (in_array($data['booking_status'], $cancelStatus)) {
                    if ((!in_array($old_service_status, $cancellableStatus) || !$model->getGroomerId()) && ($currentTimeStamp > $model->getScheduledTimestamp())) {
                        $this->messageManager->addErrorMessage(__('Not allowed to cancel this booking.'));
                        return $result;
                    }
                }
                $data['created_by'] = $current_user->getName().' ('.$current_user->getRole()->getRoleName().')';
                $data['service_id'] = $data['entity_id'];
                $data['service_status'] = $data['booking_status'];

                unset($data['entity_id']);
                unset($data['booking_status']);

                try {
                    if (!empty($model->getGroomerId())) {
                        $groomer = $this->groomerFactory->create()->load($model->getGroomerId());
                        $model->setBookingStatus($data['service_status']);
                        $comment->setData($data);
                        $model->save();
                        $comment->save();
                        if($old_service_status != $old_service_status) {
                            $this->notifyCustomerAndGroomer();
                        }
                        if (in_array($model->getBookingStatus(), $cancelStatus)) {
                            if (!empty($model->getWalletMoney())) {
                                $total = $model->getWalletMoney();
                                $wallet = $this->walletFactory->create();
                                $wData['comment'] = "Grooming Service Refund";
                                $wData['amount'] = $total;
                                $wData['flag'] = 1;
                                $wData['performed_by'] = "admin";
                                $wData['visibility'] = 1;
                                $wData['customer_id'] = $model->getCustomerId();
                                $wallet->setData($wData);
                                $wallet->save();
                                $customerData = $customer->getDataModel();
                                $totalBalance = is_null($customer->getWalletBalance()) ? "0" : $customer->getWalletBalance();
                                $balance = $totalBalance + $total;
                                $customerData->setCustomAttribute('wallet_balance',$balance);
                                $customer->updateData($customerData);
                                $customerResource = $this->customerResourceFactory->create();
                                $customerResource->saveAttribute($customer, 'wallet_balance');
                                $groomerTemplateVariable = array(
                                    'email_type' => 'cancel_notify_groomer',
                                    'email' => $groomer->getEmail(),
                                    'vars' => array(
                                        'booking_id' => $model->getEntityId(),
                                        'name' => $groomer->getName(),
                                    )
                                );
                                $customerTemplateVariable = array(
                                    'email_type' => 'cancel_notify_customer',
                                    'email' => $customer->getEmail(),
                                    'vars' => array(
                                        'booking_id' => $model->getEntityId(),
                                        'name' => $customer->getName(),
                                    )
                                );
                                $this->notifyEmailHelper->sendMail($groomerTemplateVariable);
                                $this->notifyEmailHelper->sendMail($customerTemplateVariable);
                                /*send copy to admin*/
                                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                                $email = $this->scopeConfig->getValue('msggateway/servicesbookingemail/copy_of_booking', $storeScope);
                                $email = trim($email);
                                $email = explode(',', $email);
                                $adminTemplateVariable = array(
                                    'email_type' => 'cancel_notify_customer',
                                    'email' => $email,
                                    'vars' => array(
                                        'booking_id' => $model->getEntityId(),
                                        'name' => '',
                                    )
                                );
                                $this->notifyEmailHelper->sendMail($adminTemplateVariable);
                                $cancelVar['service'] = $model->getCenter();
                                $cancelVar['name'] = $model->getPetName();
                                $cancelVar['hours'] = $model->getScheduledTime();
                                $cancelVar['date'] = $model->getScheduledDate();
                                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                                $bitly = $this->scopeConfig->getValue('groomingservice/feedback/feedback_bitly_url', $storeScope);
                                $cancelVar['url'] = !empty($bitly) ? $bitly : '';
                                $cancelVar['mobileNo'] = $model->getPhoneNo();
                                if ($model->getCenter() == "At Home"){
                                    $cancelVar['templateid'] = 'msggateway/servicesbookingemail/cancel_booking_sms';
                                    $this->notifyEmailHelper->sendCancelSms($cancelVar);
                                } elseif ($model->getCenter() == "At Experience Center"){
                                    $cancelVar['templateid'] = 'msggateway/servicesbookingemail/cancel_booking_center_sms';
                                    $this->notifyEmailHelper->sendCancelSms($cancelVar);
                                }
                            }
                        }
                        $statusVar['pet_name'] = $model->getPetName();
                        $statusVar['booking_id'] = $model->getEntityId();
                        $statusVar['status'] = $model->getBookingStatus();
                        $templateVariable['email'] = $customer->getEmail();
                        $url = $this->storeManager->getStore()->getBaseUrl().'sales/orders/viewbooking/booking_id/'.$model->getEntityId();
                        $templateVariable['cancelDetails'] = array('pet_name' => $model->getPetName(), 'booking_id' => $model->getEntityId(), 'status' => $model->getBookingStatus(), 'url' => $url);
                        $templateVariable['template_id'] = 'msggateway/servicesbookingemail/update_booking_status_email';
                        if ($model->getCenter() == "At Home"){
                            $statusVar['mobileNo'] = $customer->getPhoneNumber();
                            $statusVar['templateid'] = 'msggateway/servicesbookingemail/update_booking_status__sms';
                            $this->notifyEmailHelper->sendBookingStatusSms($statusVar);
                        } elseif ($model->getCenter() == "At Experience Center"){
                            $statusVar['mobileNo'] = $customer->getPhoneNumber();
                            $statusVar['templateid'] = 'msggateway/servicesbookingemail/update_booking_status__sms';
                            $this->notifyEmailHelper->sendBookingStatusSms($statusVar);
                        }
                        $this->helperData->sendMail($templateVariable);
                        /*send copy to admin*/
                        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                        $email = $this->scopeConfig->getValue('msggateway/servicesbookingemail/copy_of_booking', $storeScope);
                        if (!empty($email)) {
                            $email = trim($email);
                            $adminVariable['email'] = explode(',', $email);
                            $adminVariable['cancelDetails'] = array('pet_name' => $model->getPetName(), 'booking_id' => $model->getEntityId(), 'status' => $model->getBookingStatus(), 'url' => $url);
                            $adminVariable['template_id'] = 'msggateway/servicesbookingemail/update_booking_status_email';
                            $this->helperData->sendMail($adminVariable);
                        }
                        $this->messageManager->addSuccessMessage(__('Grooming status updated.'));
                        return $result;
                    } else {
                        $this->messageManager->addErrorMessage(__('Please assign a groomer.'));
                        return $result;
                    }
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage($e, __('Something went wrong while updating the Grooming service status.'));
                    return $result;
                }
            }
            $this->messageManager->addErrorMessage(__('Invalid form data.'));
            return $result;
        } catch (\Exception $e) {
            print_r($e->getMessage());
            exit();
                // $this->messageManager->addExceptionMessage($e, __('Something went wrong while updating the Grooming service status.'));
        }
    }

    public function notifyCustomerAndGroomer() {
        //TODO Send email and sms
    }
}

