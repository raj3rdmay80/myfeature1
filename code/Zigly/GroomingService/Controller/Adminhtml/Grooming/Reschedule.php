<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Controller\Adminhtml\Grooming;

use Magento\Framework\Controller\ResultFactory;

class Reschedule extends \Magento\Backend\App\Action
{

    /**
     * Reschedule page
     * 
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Zigly_GroomingService::zigly_groomingservice_grooming');
        $resultPage->getConfig()->getTitle()->prepend(__("Reschedule"));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_GroomingService::Grooming_reschedule_service');
    }
}
