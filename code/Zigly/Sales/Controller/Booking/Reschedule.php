<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
namespace Zigly\Sales\Controller\Booking;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Result\PageFactory;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Framework\Exception\NotFoundException;

class Reschedule extends \Magento\Framework\App\Action\Action
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
     * @param PageFactory $resultPageFactory
     * @param CustomerSession $customerSession
     * @param GroomingFactory $grooming
     */
    public function __construct(
        Context $context,
        GroomingFactory $bookingFactory,
        CustomerSession $customerSession,
        PageFactory $resultPageFactory
    ) {
        $this->customerSession = $customerSession;
        $this->booking = $bookingFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $customerId = $this->customerSession->getCustomerId();
        $bookingId = $this->getRequest()->getParam('id');
        if ($this->customerSession->isLoggedIn() && !empty($bookingId)) {
            $booking = $this->booking->create()->load($bookingId);
            if (!empty($booking) && $booking->getCustomerId() == $customerId) {
                $resultPage = $this->resultPageFactory->create();
                $resultPage->getConfig()->getTitle()->prepend(__('Booking Reschedule'));
                return $resultPage;
            } else {
                throw new NotFoundException(__('noroute'));
            }
        } else {
            throw new NotFoundException(__('noroute'));
        }
    }
}