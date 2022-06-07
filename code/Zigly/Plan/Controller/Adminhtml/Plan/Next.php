<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Plan
 */
declare(strict_types=1);

namespace Zigly\Plan\Controller\Adminhtml\Plan;

use Magento\Backend\App\Action\Context;
use Zigly\Plan\Model\ResourceModel\Plan\CollectionFactory;

class Next extends \Magento\Backend\App\Action
{

    /**
     * @var CollectionFactory $collectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Context $context
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * add plan form
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $planCollection = $this->collectionFactory->create();
        $planCollection->addFieldToFilter('species', $data['species']);
        $planCollection->addFieldToFilter('status', 1); 
        $limit=4; 
        // if($limit <= $planCollection->count()) {
        //     $this->messageManager->addErrorMessage(__('Maximum of 4 plans are already enabled for the selected breed.'));
        //      return $resultRedirect->setPath('*/plan/addbreed');
        // } else {
        $species = $data['species'];
        return $resultRedirect->setPath('zigly_plan/plan/new', ['breed' => $species]);
        // }

    }
}
