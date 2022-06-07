<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Activities
 */
declare(strict_types=1);

namespace Zigly\Activities\Controller\Adminhtml\Activities;

use Zigly\Activities\Model\ActivitiesFactory;
use Magento\Framework\Exception\LocalizedException;
use Zigly\Activities\Model\ResourceModel\Activities\CollectionFactory;

class Save extends \Magento\Backend\App\Action
{

    /** @var $dataPersistor*/
    protected $dataPersistor;

    /**
     * @var CollectionFactory $collectionFactory
     */
    protected $collectionFactory;

    /**
     * @param ActivitiesFactory $activitiesFactory
     * @param CollectionFactory $CollectionFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        ActivitiesFactory $activitiesFactory,
        CollectionFactory $collectionFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->authSession = $authSession;
        $this->dataPersistor = $dataPersistor;
        $this->collectionFactory = $collectionFactory;
        $this->activitiesFactory = $activitiesFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Activities::Activities_save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('activities_id');
            $data['updated_by'] = $this->authSession->getUser()->getUsername();
            $data['is_active'] = '1';
            $data['activity_name'] = trim($data['activity_name']);
            $model = $this->activitiesFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Activities no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            try {
                $activityCollection = $this->collectionFactory->create();
                $activityCollection->addFieldToFilter('activities_id', array('neq' => $id));
                $activityCollection->addFieldToFilter('species', array('eq' => $data['species']));
                $flag = 1;
                foreach ($activityCollection as $value) {
                    if ($data['activity_name'] && $value['activity_name'] == $data['activity_name']) {
                        $flag = 0;
                    }
                }
                if ($flag == 1) {
                    $model->setData($data);
                    $model->save();
                    $this->messageManager->addSuccessMessage(__('You saved the Activities.'));
                    $this->dataPersistor->clear('zigly_activities_activities');
                } else {
                    $this->messageManager->addErrorMessage(__('Activity name already exists.'));
                }
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['activities_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Activities.'));
            }
        
            $this->dataPersistor->set('zigly_activities_activities', $data);
            return $resultRedirect->setPath('*/*/edit', ['activities_id' => $this->getRequest()->getParam('activities_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

