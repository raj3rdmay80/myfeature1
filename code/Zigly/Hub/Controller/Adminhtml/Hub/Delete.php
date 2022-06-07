<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Hub
 */
declare(strict_types=1);

namespace Zigly\Hub\Controller\Adminhtml\Hub;

class Delete extends \Zigly\Hub\Controller\Adminhtml\Hub
{

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
        $id = $this->getRequest()->getParam('hub_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Zigly\Hub\Model\Hub::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Hub.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['hub_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Hub to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Hub::Hub_delete');
    }
}

