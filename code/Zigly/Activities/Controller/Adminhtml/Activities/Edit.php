<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Activities
 */
declare(strict_types=1);

namespace Zigly\Activities\Controller\Adminhtml\Activities;

class Edit extends \Zigly\Activities\Controller\Adminhtml\Activities
{

    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Activities::Activities_update');
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('activities_id');
        $model = $this->_objectManager->create(\Zigly\Activities\Model\Activities::class);
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Activities no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('zigly_activities_activities', $model);
        
        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Activities') : __('New Activities'),
            $id ? __('Edit Activities') : __('New Activities')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Activitiess'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit %1', $model->getActivityName()) : __('New Activities'));
        return $resultPage;
    }
}

