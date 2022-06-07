<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Controller\Grooming;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use \Magento\Framework\Exception\NotFoundException;

/**
 * Customer GroomingService controller
 */
class Home extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory
    ) {
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            // throw new NotFoundException(__('Please login to access.'));
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set("Dog Grooming Service at Home in Delhi/ NCR- Dog Bath, Dog Haircut, De-matting Dog");
        $resultPage->getConfig()->setDescription("Dog grooming at home services by expert groomers in Delhi/NCR- Dog Bath, Dog Haircuts, Anti- tick treatment, De-matting Dog. Book an appointment Now!");
        $resultPage->getConfig()->setKeywords("Dog Grooming dog grooming at home service dog haircuts, Dematting Dog, Anti-tick treatment, Dog bath, Dog grooming at home");
        return $resultPage;
    }
}

