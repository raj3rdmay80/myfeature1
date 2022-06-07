<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Controller\Adminhtml\Vet;

use Magento\Framework\Controller\ResultFactory;

class Addvet extends \Magento\Backend\App\Action
{

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Zigly_GroomingService::zigly_groomingservice_grooming');
        $resultPage->getConfig()->getTitle()->prepend(__("Reassign Vet"));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_GroomingService::Grooming_add_grommer');
    }
}