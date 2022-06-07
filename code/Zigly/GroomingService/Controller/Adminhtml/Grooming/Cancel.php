<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Controller\Adminhtml\Grooming;

use Magento\Customer\Model\Customer;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Framework\Exception\LocalizedException;
use Zigly\GroomingService\Model\CommentFactory;
use Magento\Framework\UrlInterface;
use Zigly\GroomingService\Model\GroomingFactory;
use Zigly\GroomingService\Model\Grooming as GroomingStatus;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Zigly\GroomingService\Helper\NotifyEmail as EmailHelper;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\CustomerFactory as CustomerResourceFactory;
use Zigly\Groomer\Model\GroomerFactory;
use Zigly\GroomingService\Helper\ServiceStatus;

class Cancel extends \Magento\Backend\App\Action
{

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param AdminSession $authSession
     * @param CommentFactory $commentFactory
     * @param GroomingFactory $groomingFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlInterface $urlInterface
     * @param Customer $customer
     * @param WalletFactory $walletFactory
     * @param EmailHelper $emailHelper
     * @param CustomerFactory $customerFactory
     * @param CustomerResourceFactory $customerResourceFactory
     * @param GroomerFactory $groomerFactory
     * @param ServiceStatus $serviceStatus
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        AdminSession $authSession,
        Customer $customer,
        WalletFactory $walletFactory,
        CommentFactory $commentFactory,
        ScopeConfigInterface $scopeConfig,
        GroomingFactory $groomingFactory,
        EmailHelper $emailHelper,
        UrlInterface $urlInterface,
        CustomerFactory $customerFactory,
        CustomerResourceFactory $customerResourceFactory,
        GroomerFactory $groomerFactory,
        ServiceStatus $serviceStatus
    ) {
        $this->authSession = $authSession;
        $this->scopeConfig = $scopeConfig;
        $this->customer = $customer;
        $this->walletFactory = $walletFactory;
        $this->urlInterface =$urlInterface;
        $this->commentFactory = $commentFactory;
        $this->groomingFactory = $groomingFactory;
        $this->emailHelper = $emailHelper;
        $this->customerFactory = $customerFactory;
        $this->customerResourceFactory = $customerResourceFactory;
        $this->groomerFactory = $groomerFactory;
        $this->serviceStatus = $serviceStatus;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_GroomingService::Grooming_cancel_service');
    }

    /**
     * Cancel action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('service_id');
        if ($id) {
            try {
                $current_user = $this->authSession->getUser();
                $cancellableStatus = $this->serviceStatus->getCancellableStatus();
                $service = $this->groomingFactory->create()->load($id);
                if (in_array($service->getBookingStatus(), $cancellableStatus) && $service->getGroomerId()) {
                    $customer = $this->customerFactory->create()->load($service->getCustomerId());
                    $groomer = $this->groomerFactory->create()->load($service->getGroomerId());
                    $comment = $this->commentFactory->create();
                    $comment->setData('service_id', $id);
                    $comment->setData('service_status', GroomingStatus::STATUS_CANCELLED_BY_ADMIN);
                    $comment->setData('comment', 'Booking service has been cancelled by admin.');
                    $comment->setData('created_by', $current_user->getName().' ('.$current_user->getRole()->getRoleName().')');
                    $comment->save();
                    $groomerTemplateVariable = array(
                        'email_type' => 'cancel_notify_groomer',
                        'email' => $groomer->getEmail(),
                        'vars' => array(
                            'booking_id' => $id,
                            'name' => $groomer->getName(),
                        )
                    );
                    $customerTemplateVariable = array(
                        'email_type' => 'cancel_notify_customer',
                        'email' => $customer->getEmail(),
                        'vars' => array(
                            'booking_id' => $id,
                            'name' => $customer->getName(),
                        )
                    );
                    $service->setBookingStatus(GroomingStatus::STATUS_CANCELLED_BY_ADMIN);
                    if (!empty($service->getWalletMoney())) {
                        $total = $service->getWalletMoney();
                        $model = $this->walletFactory->create();
                        $data['comment'] = "Grooming Service Refund";
                        $data['amount'] = $total;
                        $data['flag'] = 1;
                        $data['performed_by'] = "admin";
                        $data['visibility'] = 1;
                        $data['customer_id'] = $service->getCustomerId();
                        $model->setData($data);
                        $model->save();
                        $customerModel = $this->customer->load($service->getCustomerId());
                        $customerData = $customerModel->getDataModel();
                        $totalBalance = is_null($customerModel->getWalletBalance()) ? "0" : $customerModel->getWalletBalance();
                        $balance = $totalBalance + $total;
                        $customerData->setCustomAttribute('wallet_balance',$balance);
                        $customerModel->updateData($customerData);
                        $customerResource = $this->customerResourceFactory->create();
                        $customerResource->saveAttribute($customerModel, 'wallet_balance');
                    }
                    $this->emailHelper->sendMail($groomerTemplateVariable);
                    $this->emailHelper->sendMail($customerTemplateVariable);
                    /*send copy to admin*/
                    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                    $email = $this->scopeConfig->getValue('msggateway/servicesbookingemail/copy_of_booking', $storeScope);
                    $email = trim($email);
                    $email = explode(',', $email);
                    $adminTemplateVariable = array(
                        'email_type' => 'cancel_notify_customer',
                        'email' => $email,
                        'vars' => array(
                            'booking_id' => $id,
                            'name' => '',
                        )
                    );
                    $this->emailHelper->sendMail($adminTemplateVariable);
                    $cancelVar['service'] = $service->getCenter();
                    $cancelVar['name'] = $service->getPetName();
                    $cancelVar['hours'] = $service->getScheduledTime();
                    $cancelVar['date'] = $service->getScheduledDate();
                    $cancelVar['url'] = $this->urlInterface->getUrl("sales/orders/viewbooking",["booking_id" => $service->getEntityId()]);
                    $cancelVar['mobileNo'] = $service->getPhoneNo();
                    if ($service->getCenter() == "At Home"){
                        $cancelVar['templateid'] = 'msggateway/servicesbookingemail/cancel_booking_sms';
                        $this->emailHelper->sendCancelSms($cancelVar);
                    } elseif ($service->getCenter() == "At Experience Center"){
                        $cancelVar['templateid'] = 'msggateway/servicesbookingemail/cancel_booking_center_sms';
                        $this->emailHelper->sendCancelSms($cancelVar);
                    }
                    $service->save();
                    $this->messageManager->addSuccess(__('Booking service has been cancelled.'));
                } else {
                    $this->messageManager->addError(__('Can\'t able to cancel the booking.'));
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while cancelling the Grooming service.'));
            }
            
        } else {
             $this->messageManager->addErrorMessage(__('Entity id missing.'));
        }
        return $resultRedirect->setPath('*/*/view', ['entity_id' => $this->getRequest()->getParam('service_id')]);
    }
}
