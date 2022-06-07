<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Controller\Adminhtml\GroomingSlotTable;

class Delete extends \Zigly\ScheduleManagementApi\Controller\Adminhtml\GroomingSlotTable
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
        $id = $this->getRequest()->getParam('groomingslottable_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Zigly\ScheduleManagementApi\Model\GroomingSlotTable::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Grooming slot table.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['slot_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Grooming slot table to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

