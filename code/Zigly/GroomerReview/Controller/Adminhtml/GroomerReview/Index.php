<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomerReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Controller\Adminhtml\GroomerReview;

use Zigly\GroomerReview\Model\GroomerReview;

class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;

    protected $groomerReview;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        GroomerReview $groomerReview
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->groomerReview = $groomerReview;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_GroomerReview::GroomerReview_view');
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Zigly_GroomerReview::zigly_groomerreview_groomerreview');
        $resultPage->getConfig()->getTitle()->prepend(__("Manage Professionals Review"));
        return $resultPage;
    }

    public function groomerReviewModel()
    {
        return  $this->groomerReview->create();
    }
}

