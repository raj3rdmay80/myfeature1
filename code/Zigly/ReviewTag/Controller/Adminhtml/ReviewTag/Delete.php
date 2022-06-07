<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Controller\Adminhtml\ReviewTag;

use Zigly\ReviewTag\Model\ReviewTagFactory;

class Delete extends \Zigly\ReviewTag\Controller\Adminhtml\ReviewTag
{

/*
    @var \Magento\Framework\Registry
*/
    protected $resultPageFactory;

/*
    @var \Zigly\ReviewTag\Model\ReviewTag
*/
    protected $reviewTagFactory;


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
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('reviewtag_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->reviewTagFactory->create();
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the review tag.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['reviewtag_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a review tag to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_ReviewTag::ReviewTag_delete');
    }
}

