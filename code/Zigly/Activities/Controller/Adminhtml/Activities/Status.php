<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Activities
 */
declare(strict_types=1);

namespace Zigly\Activities\Controller\Adminhtml\Activities;

use Zigly\Activities\Model\ActivitiesFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class Status extends \Magento\Backend\App\Action
{

    /* @var $dateTimeFactory*/
    protected $dateTimeFactory;

    /**
     * @param Context $context
     * @param ActivitiesFactory $activitiesFactory
     * @param Session $authSession
     * @param DateTimeFactory $dateTimeFactory
     */
    public function __construct(
        Context $context,
        Session $authSession,
        DateTimeFactory $dateTimeFactory,
        ActivitiesFactory $activitiesFactory
    ) {
        $this->context = $context;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->authSession = $authSession;
        $this->activitiesFactory = $activitiesFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Activities::Activities_status');
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
        $id = $this->getRequest()->getParam('activities_id');
        if ($id) {
            try {
                $activity = $this->activitiesFactory->create();
                $activity->load($id);
                $status = $this->getRequest()->getParam('value');
                $updateBy = $this->authSession->getUser()->getUsername();
                $activity->setData('is_active', $status);
                $activity->setData('updated_by', $updateBy);
                $dateModel = $this->dateTimeFactory->create();
                $date = $dateModel->gmtDate();
                $activity->setData('updated_at', $date);
                $activity->save();
                $this->messageManager->addSuccessMessage(__('You changed the status.'));
                return $resultRedirect->setPath('*/*/edit', ['activities_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['activities_id' => $id]);
            }
        }
    }
}

