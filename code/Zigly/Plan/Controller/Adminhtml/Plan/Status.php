<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Plan
 */
declare(strict_types=1);

namespace Zigly\Plan\Controller\Adminhtml\Plan;

use Zigly\Plan\Model\PlanFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Zigly\Plan\Model\ResourceModel\Plan\CollectionFactory;

class Status extends \Magento\Backend\App\Action
{
     /**
     * @var CollectionFactory $collectionFactory
     */
    protected $collectionFactory;

    /* @var $dateTimeFactory*/
    protected $dateTimeFactory;

    /**
     * @param Context $context
     * @param PlanFactory $planFactory
     * @param Session $authSession
     * @param DateTimeFactory $dateTimeFactory
     */
    public function __construct(
        Context $context,
        Session $authSession,
        DateTimeFactory $dateTimeFactory,
        PlanFactory $planFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->context = $context;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->authSession = $authSession;
        $this->planFactory = $planFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Plan::Plan_status');
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
        $id = $this->getRequest()->getParam('plan_id');
        if ($id) {
            try {
                $activity = $this->planFactory->create();
                $activity->load($id);
                $planCollection = $this->collectionFactory->create();
                $planCollection->addFieldToFilter('species', $activity->getSpecies());
                $planCollection->addFieldToFilter('status', 1);
                $limit=4;
                $status = $this->getRequest()->getParam('value');
                // if($limit <= $planCollection->count() && $status == '1') {
                //     // $this->messageManager->addErrorMessage(__('Maximum of 4 plans are already enabled.'));
                //     // return $resultRedirect->setPath('*/*/edit', ['plan_id' => $id]);
                // } else {
                $updateBy = $this->authSession->getUser()->getUsername();
                $activity->setData('status', $status);
                $activity->setData('updated_by', $updateBy);
                $dateModel = $this->dateTimeFactory->create();
                $date = $dateModel->gmtDate();
                $activity->setData('updated_at', $date);
                $activity->save();
                $this->messageManager->addSuccessMessage(__('You changed the status.'));
                return $resultRedirect->setPath('*/*/edit', ['plan_id' => $id]);
                // }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['plan_id' => $id]);
            }
        }
    }
}

