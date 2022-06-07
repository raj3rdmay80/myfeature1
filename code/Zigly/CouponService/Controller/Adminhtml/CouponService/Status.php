<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_CouponService
 */
declare(strict_types=1);

namespace Zigly\CouponService\Controller\Adminhtml\CouponService;

use Zigly\CouponService\Model\CouponServiceFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class Status extends \Magento\Backend\App\Action
{

    /* @var $dateTimeFactory*/
    protected $dateTimeFactory;

    /**
     * @param Context $context
     * @param CouponServiceFactory $couponServiceFactory
     * @param DateTimeFactory $dateTimeFactory
     */
    public function __construct(
        Context $context,
        DateTimeFactory $dateTimeFactory,
        CouponServiceFactory $couponServiceFactory
    ) {
        $this->context = $context;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->couponServiceFactory = $couponServiceFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_CouponService::CouponService_status');
    }

    /**
     * status action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('couponservice_id');
        if ($id) {
            try {
                $coupon = $this->couponServiceFactory->create();
                $coupon->load($id);
                $status = $this->getRequest()->getParam('value');
                $coupon->setData('status', $status);
                $dateModel = $this->dateTimeFactory->create();
                $date = $dateModel->gmtDate();
                $coupon->setData('updated_at', $date);
                $coupon->save();
                $this->messageManager->addSuccessMessage(__('You changed the status.'));
                return $resultRedirect->setPath('*/*/edit', ['couponservice_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['couponservice_id' => $id]);
            }
        }
    }
}