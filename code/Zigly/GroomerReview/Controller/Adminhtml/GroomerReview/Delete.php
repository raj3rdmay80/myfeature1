<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomerReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Controller\Adminhtml\GroomerReview;

class Delete extends \Zigly\GroomerReview\Controller\Adminhtml\GroomerReview
{
    /*
        @var GroomerReviewFactory
    */
        protected $review;
    /*
        @var GroomerReviewFactory
    */
        protected $collection;
    /*
        @var GroomerFactory
    */
        protected $groomerFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Zigly\GroomerReview\Model\GroomerReviewFactory $review
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Zigly\GroomerReview\Model\GroomerReviewFactory $review,
        \Zigly\GroomerReview\Model\ResourceModel\GroomerReview\CollectionFactory $collection,
        \Zigly\Groomer\Model\GroomerFactory $groomerFactory

    ) {
        $this->review = $review;
        $this->collection = $collection;
        $this->groomerFactory = $groomerFactory;
        parent::__construct($context, $coreRegistry);
    }
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('groomerreview_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->review->create();
                $model->load($id);
                $groomer_id = $model->getData('groomer_id');
                $status = $model->getData('is_active');
                $model->delete();
                if($status)
                {
                    $this->setGroomerRating($groomer_id);
                }
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Professionals review.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['groomerreview_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Professionals review to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_GroomerReview::GroomerReview_delete');
    }
    /**
     * Set overall rating in groomer.
     *
     * @param string $groomer_id
     *
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

