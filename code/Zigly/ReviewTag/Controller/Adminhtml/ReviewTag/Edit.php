<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Controller\Adminhtml\ReviewTag;

use Zigly\ReviewTag\Model\ReviewTagFactory; 

class Edit extends \Zigly\ReviewTag\Controller\Adminhtml\ReviewTag
{

    protected $resultPageFactory;

    protected $reviewTag;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param ReviewTag $reviewTag
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        ReviewTagFactory $reviewTagFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->reviewTagFactory = $reviewTagFactory;
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
        $id = $this->getRequest()->getParam('reviewtag_id');
        $model = $this->reviewTagFactory->create();
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Review Tag no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('zigly_reviewtag_reviewtag', $model);
        
        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Review Tag') : __('New Review Tag'),
            $id ? __('Edit Review Tag') : __('New Review Tag')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Review Tag'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit %1', $model->getLocation()) : __('New Review Tag'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_ReviewTag::ReviewTag_update');
    }
}

