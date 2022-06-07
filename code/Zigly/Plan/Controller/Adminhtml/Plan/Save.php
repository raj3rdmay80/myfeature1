<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Plan
 */
declare(strict_types=1);

namespace Zigly\Plan\Controller\Adminhtml\Plan;

use Zigly\Plan\Model\ImageUploader;
use Zigly\Plan\Model\PlanFactory;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\App\Action\Context;
use Zigly\Activities\Model\ActivitiesFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Request\DataPersistorInterface;
use Zigly\Plan\Model\ResourceModel\Plan\CollectionFactory;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @var DataPersistorInterface $dataPersistor
     */
    protected $dataPersistor;

    /**
     * @var CollectionFactory $collectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Session $authSession
     * @param Context $context
     * @param PlanFactory $planFactory
     * @param ImageUploader $imageUploader
     * @param ActivitiesFactory $activitiesFactory
     * @param CollectionFactory $CollectionFactory
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        Session $authSession,
        PlanFactory $planFactory,
        ImageUploader $imageUploader,
        ActivitiesFactory $activitiesFactory,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor
    ) {
        $this->authSession = $authSession;
        $this->planFactory = $planFactory;
        $this->imageUploader = $imageUploader;
        $this->dataPersistor = $dataPersistor;
        $this->activitiesFactory = $activitiesFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
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
            $id = $this->getRequest()->getParam('plan_id');
            $data['updated_by'] = $this->authSession->getUser()->getUsername();
            $data['plan_name'] = trim($data['plan_name']);
            if (isset($data['plan_image'][0]['name']) && isset($data['plan_image'][0]['tmp_name'])) {
                $data['plan_image'] = $data['plan_image'][0]['name'];
                $this->imageUploader->moveFileFromTmp($data['plan_image']);
            } elseif (isset($data['plan_image'][0]['name']) && !isset($data['plan_image'][0]['tmp_name'])) {
                $data['plan_image'] = $data['plan_image'][0]['name'];
            } else {
                $data['plan_image'] = '';
            }
            if (isset($data['banner_image'][0]['name']) && isset($data['banner_image'][0]['tmp_name'])) {
                $data['banner_image'] = $data['banner_image'][0]['name'];
                $this->imageUploader->moveFileFromTmp($data['banner_image']);
            } elseif (isset($data['banner_image'][0]['name']) && !isset($data['banner_image'][0]['tmp_name'])) {
                $data['banner_image'] = $data['banner_image'][0]['name'];
            } else {
                $data['banner_image'] = '';
            }
            $model = $this->planFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Plan no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            if ($model->getStatus() == '') {
                $data['status'] = '1';
            }

            try {
                if ($data['activity']) {
                    $define = 1;
                    if (is_array($data['activity'])) {
                        $defined = $data['activity'];
                    } else {
                        $defined = explode(",", $data['activity']);
                    }
                    foreach ($defined as $value) {
                        $activity = $this->activitiesFactory->create()->load($value);
                        if($activity->getIsActive() != 1 || !$activity->getActivitiesId()){
                            $define = 0;
                        }
                    }
                    if ($define) {
                        $data['activity'] = implode(",", $data['activity']);
                    } else {
                        $this->messageManager->addError('We cant allow with disabled activity.');
                        return $resultRedirect->setPath('*/*/edit', ['plan_id' => $model->getId()]);
                    }
                }
                $citiesArray = [];
                foreach ($data['applicable_cities'] as $city) {
                    $citiesArray[] = ['like' => '%'.$city.'%'];
                }
                $planCollection = $this->collectionFactory->create();
                $planCollection->addFieldToFilter('plan_id', array('neq' => $id));
                $planCollection->addFieldToFilter('species', $data['species']);
                $planCollection->addFieldToFilter('plan_type', array('in' => [1,2,3]));
                $planCollection->addFieldToFilter(['applicable_cities'], [$citiesArray]);
                if ($planCollection) {
                    foreach ($planCollection as $value) {
                        $pln = $this->planFactory->create()->load($value['plan_id']);
                        $pln->setRecommendedPlan(0);
                        $pln->save();
                    }
                }
                /*$planCollection = $this->collectionFactory->create();
                $planCollection->addFieldToFilter('plan_id', array('neq' => $id));
                $planCollection->addFieldToFilter('species', array('eq' => $data['species']));*/
                $flag = 1;
                /*foreach ($planCollection as $value) {
                    if ($data['plan_name'] && $value['plan_name'] == $data['plan_name']) {
                        $flag = 0;
                    }
                }*/
                if (isset($data['applicable_cities'])) {
                    $data['applicable_cities'] = implode(",", $data['applicable_cities']);
                }
                if ($flag == 1) {
                    $model->setData($data);
                    $model->save();
                    $this->messageManager->addSuccessMessage(__('You saved the Plan.'));
                    $this->dataPersistor->clear('zigly_plan_plan');
                } else {
                    $this->messageManager->addErrorMessage(__('Plan name already exists.'));
                }
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['plan_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Plan.'));
            }
        
            $this->dataPersistor->set('zigly_plan_plan', $data);
            return $resultRedirect->setPath('*/*/edit', ['plan_id' => $this->getRequest()->getParam('plan_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Plan::Plan_save');
    }
}

