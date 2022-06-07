<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_CouponService
 */
declare(strict_types=1);

namespace Zigly\CouponService\Controller\Adminhtml\CouponService;

class Delete extends \Zigly\CouponService\Controller\Adminhtml\CouponService
{

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_CouponService::CouponService_delete');
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
        $id = $this->getRequest()->getParam('couponservice_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Zigly\CouponService\Model\CouponService::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Service Coupon.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['couponservice_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Service Coupon to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

