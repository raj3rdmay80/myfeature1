<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Hub
 */
declare(strict_types=1);

namespace Zigly\Hub\Controller\Adminhtml\Hub;

class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Hub::Hub_view');
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Zigly_Hub::zigly_hub_hub');
        $resultPage->getConfig()->getTitle()->prepend(__("Hub"));
        return $resultPage;
    }
}

