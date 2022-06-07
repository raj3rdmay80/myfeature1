<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Controller\Adminhtml\ReviewTag;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Zigly\ReviewTag\Model\ResourceModel\ReviewTag\CollectionFactory;

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
        return $this->_authorization->isAllowed('Zigly_ReviewTag::ReviewTag_delete');
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
        return $resultRedirect->setPath('zigly_reviewtag/reviewtag/index');
    }
 
}