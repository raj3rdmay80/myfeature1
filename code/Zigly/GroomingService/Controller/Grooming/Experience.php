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
class Experience extends Action
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
        $resultPage->getConfig()->getTitle()->set("Dog Grooming Services in Delhi/NCR- Grooming center for dogs near me");
        $resultPage->getConfig()->setDescription("Your pet deserves pampering! the zigly experience center offers a wide range of grooming services, delivered by our professional and certified groomers.");
        $resultPage->getConfig()->setKeywords("pet grooming near me; dog grooming near me; dog grooming services near me; pet grooming centre near me, grooming center for dogs near me, dog grooming centre near me");
        return $resultPage;
    }
}

