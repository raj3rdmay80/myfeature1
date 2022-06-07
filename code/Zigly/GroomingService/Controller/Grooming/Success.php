<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Controller\Grooming;

use Zigly\GroomingService\Helper\Email;
use Zigly\GroomingService\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Result\PageFactory;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Success extends \Magento\Framework\App\Action\Action
{

    /**
    * @var $pageFactory
    */
    protected $pageFactory;

    /**
     * @var Session
     */
    protected $groomingSession;

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @param Email $helperData
     * @param Context $context
     * @param Session $groomingSession
     * @param PageFactory $pageFactory
     * @param SessionFactory $customerSession
     * @param GroomingFactory $groomingFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Email $helperData,
        Context $context,
        Session $groomingSession,
        PageFactory $pageFactory,
        SessionFactory $customerSession,
        GroomingFactory $groomingFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->helperData = $helperData;
        $this->pageFactory = $pageFactory;
        $this->scopeConfig = $scopeConfig;
        $this->groomingSession = $groomingSession;
        $this->customerSession = $customerSession;
        $this->groomingFactory = $groomingFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $groomSession = $this->groomingSession->getGroomService();
        $customer = $this->customerSession->create()->getCustomer();
        $paramData = $this->getRequest()->getParams();
        /*$templateVariable['email'] = $customer->getEmail();*/
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if (!empty($groomSession['booking_id'])) {
            $bookingId = $groomSession['booking_id'];
        } else if (!empty($this->groomingSession->getGroomCenter()['booking_id'])) {
            $bookingId = $this->groomingSession->getGroomCenter()['booking_id'];
        } else if (!empty($paramData['id'])) {
            $bookingId = $paramData['id'];
        } else {
            // return $this->_redirect('/');
        }

        $bookingDetails = $this->groomingFactory->create()->load($bookingId);
        if (($bookingDetails->getBookingType() == 1) && ($customer->getId() == $bookingDetails->getCustomerId())) {

            if ($bookingDetails->getCenter() == "At Home") {
                /*$templateVariable['name'] = $groomSession['pet_name'];
                $templateVariable['template_id'] = 'msggateway/servicesbookingemail/service_home_email';
                $this->helperData->sendMail($templateVariable);*/
                $bookingVar['template_id'] = 'msggateway/servicesbookingemail/service_home_mobile';
                $bookingVar['mobile_no'] = $bookingDetails->getPhoneNo();
                $bookingVar['name'] = $bookingDetails->getPetName();
                $this->helperData->sendBookingSms($bookingVar);
                /*send service copy*/
                $email = $this->scopeConfig->getValue('msggateway/servicesbookingemail/service_team_receiver_email', $storeScope);
                if (!empty($email)) {
                    $email = str_replace(' ', '', $email);
                    $email = explode(',', $email);
                    $service['email'] = $email;
                    $service['name'] = $bookingDetails->getPetName();
                    $service['template_id'] = 'msggateway/servicesbookingemail/service_team_email_template';
                    $this->helperData->sendMail($service);
                }
                $phoneNumber = $this->scopeConfig->getValue('msggateway/servicesbookingemail/service_team_receiver_phone_number', $storeScope);
                if (!empty($phoneNumber)) {
                    $phoneNumber = str_replace(' ', '', $phoneNumber);
                    $phoneNumber = explode(',', $phoneNumber);
                    $serviceVar['template_id'] = 'msggateway/servicesbookingemail/service_team_sms_template';
                    $serviceVar['mobile_no'] = $phoneNumber;
                    $this->helperData->sendServiceTeamSms($serviceVar);
                }
                return $this->pageFactory->create();
            } else if ($bookingDetails->getCenter() == "At Experience Center") {
                /*$templateVariable['name'] = $this->groomingSession->getGroomCenter()['pet_name'];
                $templateVariable['template_id'] = 'msggateway/servicesbookingemail/service_center_email';*/
                $bookingVar['template_id'] = 'msggateway/servicesbookingemail/service_center_mobile';
                $bookingVar['mobile_no'] = $customer->getPhoneNumber();
                $bookingVar['name'] = $bookingDetails->getPetName();
                /*$this->helperData->sendMail($templateVariable);*/
                $this->helperData->sendBookingSms($bookingVar);
                /*send service copy*/
                $email = $this->scopeConfig->getValue('msggateway/servicesbookingemail/service_team_center_receiver_email', $storeScope);
                if (!empty($email)) {
                    $email = str_replace(' ', '', $email);
                    $email = explode(',', $email);
                    $service['name'] = $bookingDetails->getPetName();
                    $service['email'] = $email;
                    $service['template_id'] = 'msggateway/servicesbookingemail/service_team_email_template';
                    $this->helperData->sendMail($service);
                }
                $phoneNumber = $this->scopeConfig->getValue('msggateway/servicesbookingemail/service_team_center_receiver_phone_number', $storeScope);
                if (!empty($phoneNumber)) {
                    $phoneNumber = str_replace(' ', '', $phoneNumber);
                    $phoneNumber = explode(',', $phoneNumber);
                    $serviceVar['template_id'] = 'msggateway/servicesbookingemail/service_team_sms_template';
                    $serviceVar['mobile_no'] = $phoneNumber;
                    $this->helperData->sendServiceTeamSms($serviceVar);
                }
                return $this->pageFactory->create();
            }
        }

            return $this->_redirect('/');
    }
}