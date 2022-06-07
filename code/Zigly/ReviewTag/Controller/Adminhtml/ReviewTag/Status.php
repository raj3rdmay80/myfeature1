<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Controller\Adminhtml\ReviewTag;

use Zigly\ReviewTag\Model\ReviewTagFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class Status extends \Magento\Backend\App\Action
{

    /* @var $dateTimeFactory*/
    protected $dateTimeFactory;

    /**
     * @param Context $context
     * @param ReviewTagFactory $reviewTagFactory
     * @param DateTimeFactory $dateTimeFactory
     */
    public function __construct(
        Context $context,
        DateTimeFactory $dateTimeFactory,
        ReviewTagFactory $reviewTagFactory
    ) {
        $this->context = $context;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->reviewTagFactory = $reviewTagFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_ReviewTag::ReviewTag_status');
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
        $id = $this->getRequest()->getParam('reviewtag_id');
        if ($id) {
            try {
                $reviewTag = $this->reviewTagFactory->create();
                $reviewTag->load($id);
                $status = $this->getRequest()->getParam('value');
                $reviewTag->setData('is_active', $status);
                $dateModel = $this->dateTimeFactory->create();
                $date = $dateModel->gmtDate();
                $reviewTag->setData('updated_at', $date);
                $reviewTag->save();
                $this->messageManager->addSuccessMessage(__('You changed the status.'));
                return $resultRedirect->setPath('*/*/edit', ['reviewtag_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['reviewtag_id' => $id]);
            }
        }
    }
}

