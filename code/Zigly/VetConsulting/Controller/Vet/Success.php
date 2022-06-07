<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Controller\Vet;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\SessionFactory;
use Zigly\GroomingService\Model\GroomingFactory;
use Zigly\VetConsulting\Model\Session as VetSession;

class Success extends \Magento\Framework\App\Action\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Customer session model
     *
     * @var Session
     */
    protected $customerSession;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        SessionFactory $customerSession,
        GroomingFactory $groomingFactory,
        VetSession $vetSession,
        PageFactory $resultPageFactory
    ) {
        $this->customerSession = $customerSession;
        $this->groomingFactory = $groomingFactory;
        $this->vetSession = $vetSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute success action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->customerSession->create()->isLoggedIn()) {
            return $this->_redirect('customer/account/login');
        }
        $paramData = $this->getRequest()->getParams();
        $customerId = $this->customerSession->create()->getCustomer()->getId();

        $bookingId = 0;
        if (!empty($this->vetSession->getVet()['booking_id'])) {
            $bookingId = $this->vetSession->getVet()['booking_id'];
			$orderData = new \Magento\Framework\DataObject(array('groomer_id' => $bookingId));
					$this->_eventManager->dispatch('zigly_videointegrate_display_order', ['groomer_data' => $orderData]);
            $this->vetSession->setVet([]);
        }  else if (!empty($paramData['id'])) {
            $bookingId = $paramData['id'];
        }
        if ($bookingId) {
            $bookingDetails = $this->groomingFactory->create()->load($bookingId);
            if ($customerId == $bookingDetails->getCustomerId()) {
                $resultPage = $this->resultPageFactory->create();
                $resultPage->getConfig()->getTitle()->set("Vet Consulting");
                return $resultPage;
            }
        }
        return $this->_redirect('/');
    }
}

