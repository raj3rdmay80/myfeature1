<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Activities
 */
declare(strict_types=1);

namespace Zigly\Activities\Controller\Adminhtml\Activities;

class Delete extends \Zigly\Activities\Controller\Adminhtml\Activities
{

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Activities::Activities_delete');
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
        $id = $this->getRequest()->getParam('activities_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Zigly\Activities\Model\Activities::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Activities.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['activities_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Activities to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

