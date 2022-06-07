<?php
/**
 * Copyright (C) 2020 Zigly
 * @package  Zigly_GroomerReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Controller\Adminhtml\GroomerReview;

class Edit extends \Zigly\GroomerReview\Controller\Adminhtml\GroomerReview
{

    protected $resultPageFactory;

    protected $groomerReviewFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Zigly\GroomerReview\Model\GroomerReviewFactory $groomerReviewFactory 
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->groomerReviewFactory = $groomerReviewFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('groomerreview_id');
        $model = $this->groomerReviewFactory->create();
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Professionals Review no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('zigly_groomerreview_groomerreview', $model);
        
        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Professionals Review') : __('New Professionals Review'),
            $id ? __('Edit Professionals Review') : __('New Professionals Review')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Professionals Review'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit %1', $model->getLocation()) : __('New Groomer Review'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_GroomerReview::GroomerReview_update');
    }
}

