<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Controller\Adminhtml\Grooming;

use Magento\Framework\UrlInterface;
use Magento\Framework\Exception\LocalizedException;
use Zigly\GroomingService\Model\Comment as ServiceComment;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Zigly\GroomingService\Helper\NotifyEmail as NotifyEmailHelper;
use Magento\Customer\Model\CustomerFactory;
use Zigly\Groomer\Model\GroomerFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Zigly\GroomingService\Helper\ServiceStatus;
use Zigly\GroomingService\Model\Grooming as GroomingStatus;

class Savegroomer extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param AdminSession $authSession
     * @param \Zigly\GroomingService\Model\Grooming $groomingModel
     * @param NotifyEmailHelper $notifyEmailHelper
     * @param UrlInterface $urlInterface
     * @param CustomerFactory $customerFactory
     * @param GroomerFactory $groomerFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param ServiceStatus $serviceStatus
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        AdminSession $authSession,
        \Zigly\GroomingService\Model\Grooming $groomingModel,
        NotifyEmailHelper $notifyEmailHelper,
        ScopeConfigInterface $scopeConfig,
        UrlInterface $urlInterface,
        StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory,
        GroomerFactory $groomerFactory,
        ServiceStatus $serviceStatus
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->jsonHelper = $jsonHelper;
        $this->authSession = $authSession;
        $this->scopeConfig = $scopeConfig;
        $this->groomingModel = $groomingModel;
        $this->urlInterface =$urlInterface;
        $this->notifyEmailHelper = $notifyEmailHelper;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->groomerFactory = $groomerFactory;
        $this->serviceStatus = $serviceStatus;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_GroomingService::Grooming_add_grommer');
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
        $service_id = $this->getRequest()->getParam('service_id');
        $groomer_id = $this->getRequest()->getParam('groomer_id');
        $reassignableStatus = $this->serviceStatus->getNotReassignableStatus();
        if($service_id && $groomer_id) {
            $serviceModel = $this->groomingModel->load($service_id);
            $customer = $this->customerFactory->create()->load($serviceModel->getCustomerId());
            $groomer = $this->groomerFactory->create()->load($groomer_id);
            if (!$serviceModel->getEntityId()) {
                $this->messageManager->addErrorMessage(__('This Grooming no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            if(in_array($serviceModel->getBookingStatus(), $reassignableStatus) && ($groomer->getCenter() == 'At Home')) {
                $this->messageManager->addErrorMessage(__('Cannot assign groomer for this booking.'));
                return $resultRedirect->setPath('*/*/view', ['entity_id' => $service_id]);
            }
            try {
                $serviceModel->setGroomerId($groomer_id);
                /*$url = $this->urlInterface->getUrl("sales/orders/viewbooking",["booking_id" => $serviceModel->getEntityId()]);*/
                $url = $this->storeManager->getStore()->getBaseUrl().'sales/orders/viewbooking/booking_id/'.$serviceModel->getEntityId();
                if (!empty($groomer->getEmail())) {
                    $groomerTemplateVariable = array(
                        'email_type' => 'addgroomer_notify_groomer',
                        'email' => $groomer->getEmail(),
                        'vars' => array('pet' => $serviceModel->getPetName(), 'id' => $serviceModel->getEntityId(), 'date' => $serviceModel->getScheduledDate(), 'time' => $serviceModel->getScheduledTime())
                    );
                    $this->notifyEmailHelper->sendMail($groomerTemplateVariable);
                }
                $customerTemplateVariable = array(
                    'email_type' => 'addgroomer_notify_customer',
                    'email' => $customer->getEmail(),
                    'vars' => array('pet' => $serviceModel->getPetName(), 'groomer' => $groomer->getName(), 'url' => $url)
                );
                $serviceModel->setBookingStatus(GroomingStatus::STATUS_SCHEDULED);
                $this->notifyEmailHelper->sendMail($customerTemplateVariable);
                /*send copy to admin*/
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $email = $this->scopeConfig->getValue('msggateway/servicesbookingemail/copy_of_booking', $storeScope);
                $email = trim($email);
                $email = explode(',', $email);
                $adminTemplateVariable = array(
                    'email_type' => 'addgroomer_notify_customer',
                    'email' => $email,
                    'vars' => array('pet' => $serviceModel->getPetName(), 'groomer' => $groomer->getName(), 'url' => $url)
                );
                $this->notifyEmailHelper->sendMail($adminTemplateVariable);
                $assignVar['pet'] = $serviceModel->getPetName();
                $assignVar['groomer'] = $groomer->getName();
                $statusVar['pet_name'] = $serviceModel->getPetName();
                $statusVar['booking_id'] = $serviceModel->getEntityId();
                $statusVar['status'] = GroomingStatus::STATUS_SCHEDULED;
                if ($serviceModel->getCenter() == "At Home"){
                    $assignVar['mobileNo'] = $serviceModel->getPhoneNo();
                    $assignVar['templateid'] = 'msggateway/servicesbookingemail/assign_groomer_send_home_sms';
                    $statusVar['mobileNo'] = $serviceModel->getPhoneNumber();
                    $statusVar['templateid'] = 'msggateway/servicesbookingemail/update_booking_status__sms';
                    $this->notifyEmailHelper->sendAssignGroomerSms($assignVar);
                    $this->notifyEmailHelper->sendBookingStatusSms($statusVar);
                } elseif ($serviceModel->getCenter() == "At Experience Center"){
                    $assignVar['mobileNo'] = $customer->getPhoneNumber();
                    $assignVar['templateid'] = 'msggateway/servicesbookingemail/assign_groomer_send_center_sms';
                    $statusVar['mobileNo'] = $customer->getPhoneNumber();
                    $statusVar['templateid'] = 'msggateway/servicesbookingemail/update_booking_status__sms';
                    $this->notifyEmailHelper->sendAssignGroomerSms($assignVar);
                    $this->notifyEmailHelper->sendBookingStatusSms($statusVar);
                }
                $assignedVar['pet'] = $serviceModel->getPetName();
                $assignedVar['id'] = $serviceModel->getEntityId();
                $assignedVar['date'] = $serviceModel->getScheduledDate();
                $assignedVar['time'] = $serviceModel->getScheduledTime();
                $assignedVar['mobileNo'] = $groomer->getPhoneNumber();
                $assignedVar['templateid'] = 'msggateway/servicesbookingemail/assign_reassign_groomer_send_sms';
                $this->notifyEmailHelper->sendGroomerAssignedSms($assignedVar);
                $serviceModel->save();
                $this->messageManager->addSuccessMessage(__('Groomer added to the service.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Groomer.'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Invalid Request.'));
        }
        return $resultRedirect->setPath('*/*/view', ['entity_id' => $this->getRequest()->getParam('service_id')]);
    }
}
