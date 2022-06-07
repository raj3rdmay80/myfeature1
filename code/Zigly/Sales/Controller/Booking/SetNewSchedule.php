<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
namespace Zigly\Sales\Controller\Booking;

use Zigly\Sales\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Framework\Exception\NotFoundException;
use Zigly\GroomingService\Helper\NotifyEmail as EmailHelper;
use Zigly\GroomingService\Model\Grooming as GroomingStatus;
use Magento\Framework\App\Config\ScopeConfigInterface;

class SetNewSchedule extends \Magento\Framework\App\Action\Action
{
    /**
     * Customer session model
     *
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var GroomingFactory
     */
    protected $booking;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @param Context $context
     * @param Data $helperData
     * @param EmailHelper $emailHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param PageFactory $resultPageFactory
     * @param CustomerSession $customerSession
     * @param GroomingFactory $grooming
     */
    public function __construct(
        Context $context,
        Data $helperData,
        EmailHelper $emailHelper,
        ScopeConfigInterface $scopeConfig,
        GroomingFactory $bookingFactory,
        JsonFactory $jsonResultFactory,
        CustomerSession $customerSession,
        PageFactory $resultPageFactory
    ) {
        $this->helperData = $helperData;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->booking = $bookingFactory;
        $this->emailHelper = $emailHelper;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();

        $responseData = [
            'success' => false,
            'message' => 'Something went wrong.'
        ];
        try {
            $post = $this->getRequest()->getPostValue();
            if ($this->customerSession->isLoggedIn()) {
                $customerId = $this->customerSession->getCustomerId();
                $scheduledStatus = ['Scheduled'];
                if (!empty($post['id']) && !empty($post['selected_date']) && !empty($post['selected_time'])) {
                    $booking = $this->booking->create()->load($post['id']);
                    $customer = $this->customerSession->getCustomer();
                    $gmtTimezone = new \DateTimeZone('GMT');
                    $myDateTime = new \DateTime($post['selected_date']." ".$post['selected_time'], $gmtTimezone);
                    /* Time validation start */
                    if ($booking->getCenter() == "At Home") {
                        $startHr  = $this->getConfig('zigly_timeslot/start/hours');
                        $endHr = $this->getConfig('zigly_timeslot/end/hours');
                        $endMin = $this->getConfig('zigly_timeslot/end/minutes');
                    } else {
                        $startHr  = $this->getConfig('zigly_timeslot_experience/start/hours');
                        $endHr = $this->getConfig('zigly_timeslot_experience/end/hours');
                        $endMin = $this->getConfig('zigly_timeslot_experience/end/minutes');
                    }
                    if ($endHr <= 12) {
                        $endTime = $endHr.':'.$endMin.' am';
                    } else {
                        $endTime = ((int)$endHr - 12).':'.$endMin.' pm';
                    }
                    $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                    $todayString = $now->format('Y-m-d');
                    if ($todayString == $post['selected_date'] && $now->format('H') > ($startHr - 2)) {
                        $zone = new \DateTimeZone('Asia/Kolkata');
                        $selectedDate = new \DateTime($post['selected_date']." ".$post['selected_time'], $zone);
                        $currentDateTime = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                        $currentDateTime->add(new \DateInterval('PT2H'));
                        if ($currentDateTime->format('i') > 30) {
                            $currentDateTime->add(new \DateInterval('PT1H'));
                            $currentDateTime->setTime($currentDateTime->format('H'), 00);
                        } elseif ($currentDateTime->format('i') < 30) {
                            $currentDateTime->setTime($currentDateTime->format('H'), 30);
                        }
                        $endDate = new \DateTime($post['selected_date']." ".$endTime, $zone);
                        if ($selectedDate < $currentDateTime || $currentDateTime > $endDate) {
                            $responseData['message'] = 'Invalid Data.';
                            $result->setData($responseData);
                            return $result;
                        }
                    }
                    if (!empty($booking) && $booking->getCustomerId() == $customerId) {
                        if (in_array($booking->getBookingStatus(), $scheduledStatus)) {
                            $booking->setScheduledDate($post['selected_date']);
                            $booking->setScheduledTime($post['selected_time']);
                            $selectedDatetime = \DateTime::createFromFormat("Y-m-d h:i a", $post['selected_date']." ".$post['selected_time']);
                            $booking->setScheduledTimestamp($selectedDatetime->getTimestamp());
                            $booking->setBookingStatus(GroomingStatus::STATUS_RESCHEDULED_BY_CUSTOMER);
                            $statusVar['pet_name'] = $booking->getPetName();
                            $statusVar['booking_id'] = $booking->getEntityId();
                            $statusVar['status'] = GroomingStatus::STATUS_RESCHEDULED_BY_CUSTOMER;
                            $templateVariable['email'] = $customer->getEmail();
                            $templateVariable['cancelDetails'] = array();
                            if ($booking->getCenter() == "At Home"){
                                $templateVariable['template_id'] = 'msggateway/servicesbookingemail/reschedule_send_email';
                                $scheduleVar['mobileNo'] = $booking->getPhoneNo();
                                $scheduleVar['templateid'] = 'msggateway/servicesbookingemail/reschedule_send_sms';
                                $statusVar['mobileNo'] = $booking->getPhoneNumber();
                                $statusVar['templateid'] = 'msggateway/servicesbookingemail/update_booking_status__sms';
                                $this->helperData->sendMail($templateVariable);
                                $this->emailHelper->sendRescheduleSms($scheduleVar);
                                $this->emailHelper->sendBookingStatusSms($statusVar);
                            } elseif ($booking->getCenter() == "At Experience Center"){
                                $templateVariable['template_id'] = 'msggateway/servicesbookingemail/reschedule_send_center_email';
                                $scheduleVar['mobileNo'] = $customer->getPhoneNumber();
                                $scheduleVar['templateid'] = 'msggateway/servicesbookingemail/reschedule_send_center_sms';
                                $statusVar['mobileNo'] = $customer->getPhoneNumber();
                                $statusVar['templateid'] = 'msggateway/servicesbookingemail/update_booking_status__sms';
                                $this->helperData->sendMail($templateVariable);
                                $this->emailHelper->sendRescheduleSms($scheduleVar);
                                $this->emailHelper->sendBookingStatusSms($statusVar);
                            }
                            $booking->save();
                            $responseData['success'] = true;
                            $responseData['message'] = "Successfully applied.";
                        } else {
                            $responseData['message'] = "Invalid Booking Status";
                        }
                    } else {
                        $responseData['message'] = "Booking doesn't exits";
                    }
                } else {
                    $responseData['message'] = "Invalid Data.";
                }
            } else {
                $responseData['message'] = "Please reload the page and try again.";
            }
        } catch (\Exception $e) {
            $responseData['trace'] = $e->getMessage();
        }
        $result->setData($responseData);
        return $result;
    }

    /*
    * Get Config
    */
    public function getConfig($path)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue($path, $storeScope);
    }
}