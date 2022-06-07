<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomerReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Controller\Adminhtml\GroomerReview;

use Zigly\GroomerReview\Model\GroomerReviewFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @param GroomerReviewFactory $groomerReviewFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        GroomerReviewFactory $groomerReviewFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->groomerReviewFactory = $groomerReviewFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_GroomerReview::GroomerReview_save');
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
            $id = $this->getRequest()->getParam('groomerreview_id');
            if(isset($data['tag_name']))
            {
                $tagName = $data['tag_name'];
                unset($data['tag_name']);
                $data['tag_name'] = implode(", ",$tagName);
            }
            $model = $this->groomerReviewFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Professionals Review no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            $model->setData($data);
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Professionals Review.'));
                $this->dataPersistor->clear('zigly_groomerreview_groomerreview');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['groomerreview_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Professionals review.'));
            }
            $this->dataPersistor->set('zigly_groomerreview_groomerreview', $data);
            return $resultRedirect->setPath('*/*/edit', ['groomerreview_id' => $this->getRequest()->getParam('groomerreview_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

}
