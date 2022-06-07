<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Groomer
 */
declare(strict_types=1);

namespace Zigly\Groomer\Controller\Adminhtml\Groomer;

use Zigly\Groomer\Model\GroomerFactory;
use Magento\Backend\App\Action\Context;

class Status extends \Magento\Backend\App\Action
{

    /**
     * @param Context $context
     * @param GroomerFactory $groomerFactory
     */
    public function __construct(
        Context $context,
        GroomerFactory $groomerFactory
    ) {
        $this->context = $context;
        $this->groomerFactory = $groomerFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Groomer::Groomer_status');
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
        $id = $this->getRequest()->getParam('groomer_id');
        if ($id) {
            try {
                $activity = $this->groomerFactory->create();
                $activity->load($id);
                $status = $this->getRequest()->getParam('value');
                $activity->setData('pro_status', $status);
                $activity->save();
                $this->messageManager->addSuccessMessage(__('You changed the status.'));
                return $resultRedirect->setPath('*/*/edit', ['groomer_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['groomer_id' => $id]);
            }
        }
    }
}

