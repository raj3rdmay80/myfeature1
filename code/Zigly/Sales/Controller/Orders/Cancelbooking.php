<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
namespace Zigly\Sales\Controller\Orders;

use Zigly\Sales\Helper\Data;
use Magento\Customer\Model\Customer;
use Magento\Framework\UrlInterface;
use Zigly\Wallet\Model\WalletFactory;
use Zigly\Groomer\Model\GroomerFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Result\PageFactory;
use Zigly\GroomingService\Model\CommentFactory;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Zigly\GroomingService\Model\Grooming as GroomingStatus;

class Cancelbooking extends \Magento\Framework\App\Action\Action
{

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @var PageFactory
    */
    protected $pageFactory;

    /**
     * @var CommentFactory
    */
    protected $commentFactory;

    /**
     * @param Context $context
     * @param Data $helperData
     * @param Customer $customer
     * @param UrlInterface $urlInterface
     * @param WalletFactory $walletFactory
     * @param GroomerFactory $groomerFactory
     * @param JsonFactory $resultJsonFactory
     * @param PageFactory $resultPageFactory
     * @param CommentFactory $commentFactory
     * @param SessionFactory $customerSession
     * @param CustomerFactory $customerFactory
     * @param GroomingFactory $groomingFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        Data $helperData,
        Customer $customer,
        PageFactory $pageFactory,
        UrlInterface $urlInterface,
        WalletFactory $walletFactory,
        JsonFactory $resultJsonFactory,
        CommentFactory $commentFactory,
        GroomerFactory $groomerFactory,
        SessionFactory $customerSession,
        CustomerFactory $customerFactory,
        GroomingFactory $groomingFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->customer = $customer;
        $this->helperData = $helperData;
        $this->scopeConfig = $scopeConfig;
        $this->pageFactory = $pageFactory;
        $this->urlInterface =$urlInterface;
        $this->walletFactory = $walletFactory;
        $this->groomerFactory = $groomerFactory;
        $this->commentFactory = $commentFactory;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->groomingFactory = $groomingFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $result = $this->resultJsonFactory->create();
        try {
            $bookingId = $this->getRequest()->getParam('id');
            $booking = $this->groomingFactory->create()->load($bookingId);
            $professional = $this->groomerFactory->create()->load($booking->getGroomerId());
            $currentCustomer = $this->customerSession->create()->getCustomer();
            $flag = false;
            $status = ['Scheduled', 'Inprogress', 'Rescheduled by Admin', 'Rescheduled by Customer'];
            $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
            $currentTimeStamp = $now->getTimestamp() + $now->getOffset();
            if (in_array($booking->getBookingStatus(), $status) && $booking->getGroomerId() && $currentCustomer->getId() == $booking->getCustomerId() && ($currentTimeStamp < $booking->getScheduledTimestamp()))
            {
                $url = $this->urlInterface->getUrl("sales/orders/viewbooking",["booking_id" => $booking->getEntityId()]);
                $cancelDetails = array('service' => $booking->getCenter(), 'name' => $booking->getPetName(), 'hours' => $booking->getScheduledTime(), 'date' => $booking->getScheduledDate(), 'url' => $url);
                /* comment */
                $customer = $currentCustomer->getFirstname().'(Customer)';
                $comment = $this->commentFactory->create();
                $comment->setData('service_id', $bookingId);
                $comment->setData('service_status', 'Cancelled by customer');
                $comment->setData('comment', 'Booking has been cancelled by customer');
                $comment->setData('created_by', $customer);
                $comment->save();
                /*set booking cancel status*/
                $booking->setData('booking_status', GroomingStatus::STATUS_CANCELLED_BY_CUSTOMER);
                if (!empty($booking->getWalletMoney())) {
                    $total = $booking->getWalletMoney();
                    $model = $this->walletFactory->create();
                    if ($booking->getBookingType() == 2) {
                        $data['comment'] = "Vet Consulting Refund";
                    } else {
                        $data['comment'] = "Grooming Service Refund";
                    }
                    $data['amount'] = $total;
                    $data['flag'] = 1;
                    $data['performed_by'] = "customer";
                    $data['visibility'] = 1;
                    $data['customer_id'] = $currentCustomer->getId();
                    $model->setData($data);
                    $model->save();
                    $customerModel = $this->customer->load($currentCustomer->getId());
                    $customerData = $customerModel->getDataModel();
                    $totalBalance = is_null($customerModel->getWalletBalance()) ? "0" : $customerModel->getWalletBalance();
                    $balance = $totalBalance + $total;
                    $customerData->setCustomAttribute('wallet_balance',$balance);
                    $customerModel->updateData($customerData);
                    $customerResource = $this->customerFactory->create();
                    $customerResource->saveAttribute($customerModel, 'wallet_balance');
                }
                /*cancel email to customer*/
                $templateVariable['cancelDetails'] = $cancelDetails;
                $templateVariable['email'] = $currentCustomer->getEmail();
                $templateVariable['template_id'] = 'msggateway/servicesbookingemail/cancel_booking_email';
                /*cancel email to professional*/
                $notifyGroomer['cancelDetails'] = $cancelDetails;
                $notifyGroomer['email'] = $professional->getEmail();
                $notifyGroomer['template_id'] = 'msggateway/servicesbookingemail/cancel_groomer_notiy_email';
                /*send copy to admin*/
                $notifyAdmin['cancelDetails'] = $cancelDetails;
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $email = $this->scopeConfig->getValue('msggateway/servicesbookingemail/copy_to', $storeScope);
                $email = str_replace(' ', '', $email);
                $email = explode(',', $email);
                $notifyAdmin['email'] = $email;
                $notifyAdmin['template_id'] = 'msggateway/servicesbookingemail/cancel_groomer_copy_email';
                $flag = true;
            }
            if ($flag) {
                $this->helperData->sendMail($templateVariable);
                $cancelVar['service'] = $booking->getCenter();
                $cancelVar['name'] = $booking->getPetName();
                $cancelVar['hours'] = $booking->getScheduledTime();
                $cancelVar['date'] = $booking->getScheduledDate();
                $cancelVar['url'] = $url;
                if ($booking->getCenter() == "At Home"){
                    $cancelVar['mobileNo'] = $booking->getPhoneNo();
                    $cancelVar['templateid'] = 'msggateway/servicesbookingemail/cancel_booking_sms';
                    $this->helperData->sendCancelSms($cancelVar);
                } elseif ($booking->getCenter() == "At Experience Center"){
                    $cancelVar['mobileNo'] = $currentCustomer->getPhoneNumber();
                    $cancelVar['templateid'] = 'msggateway/servicesbookingemail/cancel_booking_center_sms';
                    $this->helperData->sendCancelSms($cancelVar);
                }
                $this->helperData->sendMail($notifyGroomer);
                $this->helperData->sendMail($notifyAdmin);
                $booking->save();
                $this->messageManager->addSuccess(__('You canceled the booking successfully.'));
            } else {
                $this->messageManager->addError(__('Can\'t able to cancel the booking.'));
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
            __($e->getMessage())
            );
        }
        $this->_redirect('sales/orders/booking/');
        return $resultPage;
    }
}