<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Controller\Adminhtml\ReviewTag;

use Zigly\ReviewTag\Model\ReviewTagFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;


    /**
     * @param ReviewTagFactory $ReviewTagFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        ReviewTagFactory $reviewTagFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->reviewTagFactory = $reviewTagFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_ReviewTag::ReviewTag_save');
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
            $id = $this->getRequest()->getParam('reviewtag_id');
            $model = $this->reviewTagFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Review Tag no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            if ($model->getIsActive() == '') {
                $data['is_active'] = '1';
            }

            $model->setData($data);
        
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Review Tag.'));
                $this->dataPersistor->clear('zigly_reviewtag_reviewtag');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['reviewtag_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the review tag.'));
            }
        
            $this->dataPersistor->set('zigly_reviewtag_reviewtag', $data);
            return $resultRedirect->setPath('*/*/edit', ['reviewtag_id' => $this->getRequest()->getParam('reviewtag_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

