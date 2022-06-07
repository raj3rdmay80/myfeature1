<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Cityscreen
 */
declare(strict_types=1);

namespace Zigly\Cityscreen\Controller\Adminhtml\Cityscreen;

class Delete extends \Zigly\Cityscreen\Controller\Adminhtml\Cityscreen
{

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Cityscreen::Cityscreen_delete');
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
        $id = $this->getRequest()->getParam('cityscreen_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Zigly\Cityscreen\Model\Cityscreen::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Cityscreen.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['cityscreen_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a City to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

