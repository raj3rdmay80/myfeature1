<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
namespace Zigly\Sales\Controller\Orders;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Customer\Model\Session as CustomerSession;

class Viewbooking extends \Magento\Sales\Controller\Order\History
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
     * @param Context $context
     * @param GroomingFactory $grooming
     * @param PageFactory $resultPageFactory
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        GroomingFactory $bookingFactory,
        CustomerSession $customerSession
    ) {
        $this->booking = $bookingFactory;
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $resultPageFactory);
    }

    public function execute()
    {
        if ($this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomerId();
            $bookingId = $this->getRequest()->getParam('booking_id');
            if (!empty($bookingId)) {
                $booking = $this->booking->create()->load($bookingId);
                if (!empty($booking) && $booking->getCustomerId() == $customerId) {
                    $resultPage = $this->resultPageFactory->create();
                    $resultPage->getConfig()->getTitle()->prepend(__('My Bookings'));
                    return $resultPage;
                }
            }
        }
        throw new NotFoundException(__('noroute'));
    }
}