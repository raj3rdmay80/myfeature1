<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Hub
 */
declare(strict_types=1);

namespace Zigly\Hub\Controller\Adminhtml\Hub;

use Zigly\Hub\Model\HubFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class Status extends \Magento\Backend\App\Action
{

    /* @var $dateTimeFactory*/
    protected $dateTimeFactory;

    /**
     * @param Context $context
     * @param HubFactory $hubFactory
     * @param DateTimeFactory $dateTimeFactory
     */
    public function __construct(
        Context $context,
        DateTimeFactory $dateTimeFactory,
        HubFactory $hubFactory
    ) {
        $this->context = $context;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->hubFactory = $hubFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Hub::Hub_status');
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
        $id = $this->getRequest()->getParam('hub_id');
        if ($id) {
            try {
                $hub = $this->hubFactory->create();
                $hub->load($id);
                $status = $this->getRequest()->getParam('value');
                $hub->setData('status', $status);
                $dateModel = $this->dateTimeFactory->create();
                $date = $dateModel->gmtDate();
                $hub->setData('updated_at', $date);
                $hub->save();
                $this->messageManager->addSuccessMessage(__('You changed the status.'));
                return $resultRedirect->setPath('*/*/edit', ['hub_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['hub_id' => $id]);
            }
        }
    }
}

