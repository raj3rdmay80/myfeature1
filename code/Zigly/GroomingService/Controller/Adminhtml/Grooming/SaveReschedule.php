<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Controller\Adminhtml\Grooming;

use Magento\Framework\Exception\LocalizedException;
use Zigly\GroomingService\Model\CommentFactory;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Zigly\GroomingService\Helper\NotifyEmail as NotifyEmailHelper;
use Magento\Customer\Model\CustomerFactory;
use Zigly\Groomer\Model\GroomerFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Zigly\GroomingService\Helper\ServiceStatus;
use Zigly\GroomingService\Model\Grooming as GroomingStatus;

class saveReschedule extends \Magento\Backend\App\Action
{


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param AdminSession $authSession
     * @param \Zigly\GroomingService\Model\Grooming $groomingModel
     * @param NotifyEmailHelper $notifyEmailHelper
     * @param CustomerFactory $customerFactory
     * @param GroomerFactory $groomerFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param CommentFactory $commentFactory
     * @param ServiceStatus $serviceStatus
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        AdminSession $authSession,
        \Zigly\GroomingService\Model\Grooming $groomingModel,
        NotifyEmailHelper $notifyEmailHelper,
        CustomerFactory $customerFactory,
        GroomerFactory $groomerFactory,
        ScopeConfigInterface $scopeConfig,
        CommentFactory $commentFactory,
        ServiceStatus $serviceStatus
    ) {
        $this->authSession = $authSession;
        $this->groomingModel = $groomingModel;
        $this->notifyEmailHelper = $notifyEmailHelper;
        $this->customerFactory = $customerFactory;
        $this->scopeConfig = $scopeConfig;
        $this->groomerFactory = $groomerFactory;
        $this->commentFactory = $commentFactory;
        $this->serviceStatus = $serviceStatus;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_GroomingService::Grooming_reschedule_service');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $current_user = $this->authSession->getUser();
        $service_id = $this->getRequest()->getParam('entity_id');
        $scheduled_date = $this->getRequest()->getParam('selected_date');
        $scheduled_time = $this->getRequest()->getParam('selected_time');
        $reschedulableStatus = $this->serviceStatus->getReschedulableStatus();
        if(!$scheduled_date || !$scheduled_time) {
            $this->messageManager->addErrorMessage(__('Invalid request date or time not selected.'));
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }
        $serviceModel = $this->groomingModel->load($service_id);
        if(!in_array($serviceModel->getBookingStatus(), $reschedulableStatus)) {
            $this->messageManager->addErrorMessage(__('Cannot reschedule this booking.'));
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }
        try {
            $customer = $this->customerFactory->create()->load($serviceModel->getCustomerId());
            $groomer = $this->groomerFactory->create()->load($serviceModel->getGroomerId());
            $comment = $this->commentFactory->create();
            $comment->setData('service_id', $service_id);
            $comment->setData('service_status', GroomingStatus::STATUS_RESCHEDULED_BY_ADMIN);
            $comment->setData('comment', 'Booking service has been reschedule by admin.');
            $comment->setData('created_by', $current_user->getName().' ('.$current_user->getRole()->getRoleName().')');
            $comment->save();
            $serviceModel->setScheduledDate($scheduled_date);
            $serviceModel->setScheduledTime($scheduled_time);
            $selectedDatetime = \DateTime::createFromFormat("Y-m-d h:i a", $scheduled_date." ".$scheduled_time);
            $serviceModel->setScheduledTimestamp($selectedDatetime->getTimestamp());
            $serviceModel->setBookingStatus(GroomingStatus::STATUS_RESCHEDULED_BY_ADMIN);
            if (!empty($groomer->getEmail())) {
                $groomerTemplateVariable = array(
                    'email_type' => 'rescheduled_notify_groomer',
                    'email' => $groomer->getEmail(),
                    'vars' => array(
                        'booking_id' => $service_id,
                        'name' => $groomer->getName(),
                    )
                );
                $this->notifyEmailHelper->sendMail($groomerTemplateVariable);
            }
            $customerTemplateVariable = array(
                'email_type' => 'rescheduled_notify_customer',
                'email' => $customer->getEmail(),
                'vars' => array(
                    'booking_id' => $service_id,
                    'name' => $customer->getName(),
                )
            );
            $this->notifyEmailHelper->sendMail($customerTemplateVariable);
            /*send copy to admin*/
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $email = $this->scopeConfig->getValue('msggateway/servicesbookingemail/copy_of_booking', $storeScope);
            if (!empty($email)) {
                $email = trim($email);
                $email = explode(',', $email);
                $adminTemplateVariable = array(
                    'email_type' => 'rescheduled_notify_customer',
                    'email' => $email,
                    'vars' => array(
                        'booking_id' => $service_id,
                        'name' => '',
                    )
                );
                $this->notifyEmailHelper->sendMail($adminTemplateVariable);
            }
            $statusVar['pet_name'] = $serviceModel->getPetName();
            $statusVar['booking_id'] = $serviceModel->getEntityId();
            $statusVar['status'] = GroomingStatus::STATUS_RESCHEDULED_BY_ADMIN;
            if ($serviceModel->getCenter() == "At Home"){
                $scheduleVar['mobileNo'] = $serviceModel->getPhoneNo();
                $scheduleVar['templateid'] = 'msggateway/servicesbookingemail/reschedule_send_sms';
                $statusVar['mobileNo'] = $serviceModel->getPhoneNumber();
                $statusVar['templateid'] = 'msggateway/servicesbookingemail/update_booking_status__sms';
                $this->notifyEmailHelper->sendRescheduleSms($scheduleVar);
                $this->notifyEmailHelper->sendBookingStatusSms($statusVar);
            } elseif ($serviceModel->getCenter() == "At Experience Center"){
                $scheduleVar['mobileNo'] = $customer->getPhoneNumber();
                $scheduleVar['templateid'] = 'msggateway/servicesbookingemail/reschedule_send_center_sms';
                $statusVar['mobileNo'] = $customer->getPhoneNumber();
                $statusVar['templateid'] = 'msggateway/servicesbookingemail/update_booking_status__sms';
                $this->notifyEmailHelper->sendRescheduleSms($scheduleVar);
                $this->notifyEmailHelper->sendBookingStatusSms($statusVar);
            }
            $serviceModel->save();
            $this->messageManager->addSuccessMessage(__('Booking has been rescheduled.'));
            return $resultRedirect->setPath('*/*/view', ['entity_id' => $service_id]);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the reschedule.'));
        }
        return $resultRedirect->setPath($this->_redirect->getRefererUrl());
    }
}
