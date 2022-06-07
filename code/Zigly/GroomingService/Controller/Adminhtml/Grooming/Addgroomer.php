<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Plan
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Controller\Adminhtml\Grooming;

use Magento\Framework\Controller\ResultFactory;

class Addgroomer extends \Magento\Backend\App\Action
{
     /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Zigly\GroomingService\Model\Grooming $groomingModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Zigly\GroomingService\Model\Grooming $groomingModel
    ) {
        $this->groomingModel = $groomingModel;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Zigly_GroomingService::zigly_groomingservice_grooming');
        $resultRedirect = $this->resultRedirectFactory->create();
        $service_id = $this->getRequest()->getParam('entity_id');
        $serviceModel = $this->groomingModel->load($service_id);
        $title = __('Add Groomer');
        if ($serviceModel->getGroomerId()) {
            $title = __('Reassign Groomer');
        }
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_GroomingService::Grooming_add_grommer');
    }
}
