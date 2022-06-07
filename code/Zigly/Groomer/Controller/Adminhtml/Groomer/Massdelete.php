<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Groomer
 */
declare(strict_types=1);

namespace Zigly\Groomer\Controller\Adminhtml\Groomer;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Zigly\Groomer\Model\ResourceModel\Groomer\CollectionFactory;

class Massdelete extends \Magento\Backend\App\Action
{

    /**
     * @var Filter $filter
     */
    protected $filter;

    /**
     * @var CollectionFactory $collectionFactory
     */
    protected $collectionFactory;
 
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
 
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
 

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Groomer::Groomer_delete');
    }
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $item) {
            $item->delete();
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));
 
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('zigly_groomer/groomer/index');
    }
 
}