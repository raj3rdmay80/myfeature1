<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomerReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Controller\Adminhtml\GroomerReview;

use Zigly\GroomerReview\Model\GroomerReviewFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class Status extends \Magento\Backend\App\Action
{

    /* @var $dateTimeFactory*/
    protected $dateTimeFactory;

    /**
     * @param Context $context
     * @param GroomerReviewFactory $groomerReviewFactory
     * @param DateTimeFactory $dateTimeFactory
     */
    public function __construct(
        Context $context,
        DateTimeFactory $dateTimeFactory,
        GroomerReviewFactory $groomerReviewFactory,
        \Zigly\GroomerReview\Model\ResourceModel\GroomerReview\CollectionFactory $collection,
        \Zigly\Groomer\Model\GroomerFactory $groomerFactory
    ) {
        $this->context = $context;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->groomerReviewFactory = $groomerReviewFactory;
        $this->collection = $collection;
        $this->groomerFactory = $groomerFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_GroomerReview::GroomerReview_status');
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
        $id = $this->getRequest()->getParam('groomerreview_id');
        if ($id) {
            try {
                $groomerReview = $this->groomerReviewFactory->create();
                $groomerReview->load($id);
                $groomer_id = $groomerReview->getData('groomer_id');
                $status = $this->getRequest()->getParam('value');
                $groomerReview->setData('is_active', $status);
                $dateModel = $this->dateTimeFactory->create();
                $date = $dateModel->gmtDate();
                $groomerReview->setData('updated_at', $date);
                $groomerReview->save();
                $this->setGroomerRating($groomer_id);
                $this->messageManager->addSuccessMessage(__('You changed the status.'));
                return $resultRedirect->setPath('*/*/edit', ['groomerreview_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['groomerreview_id' => $id]);
            }
        }
    }
    /**
     * Set overall rating in groomer.
     *
     * @param string $groomer_id
     */
    public function setGroomerRating($groomer_id)
    {
        $reviewCollection = $this->collection->create()->addFieldToSelect('star_rating')->addFieldToFilter('groomer_id',"$groomer_id")->addFieldToFilter('is_active',"1");
        $totalRating = 0;
        $totalCount= 0;
        if(!empty($reviewCollection->getData()))
            {
                foreach ($reviewCollection->getData() as $value)
                {
                    $totalRating = $totalRating+$value['star_rating'];
                    $totalCount++;
                }
                $average = $totalRating/$totalCount;
                $rating = round($average, 1);
                $groomerModel = $this->groomerFactory->create()->load($groomer_id);
                $groomerModel->setData('overall_rating', $rating);
                $groomerModel->save();
        }
    }
}

