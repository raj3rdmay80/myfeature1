<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Hub
 */
declare(strict_types=1);

namespace Zigly\Hub\Controller\Adminhtml\Hub;

use Zigly\Hub\Model\HubFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    protected $regionFactory;

    /**
     * @param HubFactory $hubFactory
     * @param RegionFactory $regionFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        HubFactory $hubFactory,
        RegionFactory $regionFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->regionFactory = $regionFactory;
        $this->hubFactory = $hubFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Hub::Hub_save');
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
            $id = $this->getRequest()->getParam('hub_id');
            $region = $this->regionFactory->create()->load($data['region_id']);
            $data['state'] = $region->getName();
            $model = $this->hubFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Hub no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            if ($model->getStatus() == '') {
                $data['status'] = '1';
            }

            $model->setData($data);
        
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Hub.'));
                $this->dataPersistor->clear('zigly_hub_hub');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['hub_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Hub.'));
            }
        
            $this->dataPersistor->set('zigly_hub_hub', $data);
            return $resultRedirect->setPath('*/*/edit', ['hub_id' => $this->getRequest()->getParam('hub_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

