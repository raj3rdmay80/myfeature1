<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Plan
 */
declare(strict_types=1);

namespace Zigly\Plan\Controller\Adminhtml\Plan;

use Magento\Framework\Controller\ResultFactory;

class Addbreed extends \Magento\Backend\App\Action
{

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Zigly_Plan::zigly_plan_plan');
        $resultPage->getConfig()->getTitle()->prepend(__("Add Plan"));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Plan::Plan_save');
    }
}

