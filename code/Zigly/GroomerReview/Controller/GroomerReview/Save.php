<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomerReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Controller\GroomerReview;

use Zigly\GroomerReview\Model\GroomerReviewFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Customer\Model\SessionFactory;
use Zigly\GroomingService\Model\ResourceModel\Grooming\CollectionFactory;
use Magento\Framework\App\Request\Http;
use Zigly\GroomerReview\Helper\Email;
use Zigly\Groomer\Model\ResourceModel\Groomer\CollectionFactory as GroomerCollection;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var $groomingFactory
     */
    protected $groomingFactory;
    /**
     * @var $customerSession
     */
    protected $customerSession;
    /**
     * @var $collectionFactory
     */
    protected $collectionFactory;
    /**
     * @var $request
     */
    protected $request;
    /**
     * @var $helper
     */
    protected $helper;
    /**
     * @var $helper
     */
    protected $groomerCollection;

    /**
     * @param Context $context
     * @param GroomerReviewFactory $GroomerReviewFactory
     * @param ResultFactory $result
     * @param CollectionFactory $collectionFactory
     * @param Http $request
     */

    public function __construct(
        Context $context,
        GroomerReviewFactory $GroomerReviewFactory,
        ResultFactory $result,
        GroomingFactory $groomingFactory,
        SessionFactory $customerSession,
        CollectionFactory $collectionFactory,
        Http $request,
        Email $helper,
        GroomerCollection $groomerCollection
    )
    {

        parent::__construct($context);
        $this->groomerreview = $GroomerReviewFactory;
        $this->resultRedirect = $result;
        $this->groomingFactory = $groomingFactory;
        $this->customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
        $this->helper = $helper;
        $this->groomerCollection = $groomerCollection;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $customerData = $this->customerSession->create()->getCustomer();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        if(!empty($customerData->getId())) {
            $bookingId = $data['service_id'];
            $validation = $this->collectionFactory->create()->addFieldToSelect('entity_id')->addFieldToSelect('customer_id')->addFieldToSelect('booking_status')->addFieldToSelect('scheduled_date')->addFieldToSelect('review')->addFieldToSelect('groomer_id')->addFieldToFilter('entity_id',"$bookingId");
            $validation = $validation->getData();
            if (isset($validation) && !empty($validation)) {
                $date = new \DateTime($validation[0]['scheduled_date'].'+10 days');
                $date->setTimezone(new \DateTimeZone('Asia/Kolkata'));
                $currentTimeStamp = $date->getTimestamp() + $date->getOffset();
                $today = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                $todayTimeStamp = $today->getTimestamp() + $today->getOffset();
                if($customerData->getId() == $validation[0]['customer_id'] && !empty($validation[0]['groomer_id']) && $validation[0]['booking_status'] == 'Completed' && $validation[0]['review'] == 0 && ($todayTimeStamp < $currentTimeStamp)) {
                    $data = $this->getRequest()->getPostValue();
                    $paramValidation = $this->inputvalidation($data);
                    if($paramValidation == 'false') {
                        return $resultRedirect;
                    }
                    $model = $this->groomerreview->create();
                    if(isset($data['tag_name'])){
                        $tag_name = implode(", ", $data['tag_name']);
                        unset($data['tag_name']);
                        $data['tag_name'] = $tag_name;
                    }
                    $model->addData($data);
                    $saveData = $model->save();
                    if($saveData) {
                        $this->messageManager->addSuccess(('Thank you for sharing your feedback!'));
                        $grommingModel = $this->groomingFactory->create()->load($data['service_id']);
                        $grommingModel->addData(['review' => '1']);
                        $groomingResult = $grommingModel->save();
                        /*send the email functionality*/
                        $groomerDetails = $this->groomerCollection->create()->addFieldToSelect('email')->addFieldToSelect('phone_number')->addFieldToFilter('groomer_id',''.$validation[0]['groomer_id'].'');
                        $groomerDetails = $groomerDetails->getData();
                        if(!empty($groomerDetails)) {
                            $service['email'] = $groomerDetails[0]['email'].','.$customerData->getEmail();
                            $service['email'] = explode(",", $service['email']);
                            $service['customer_name'] = $customerData->getName();
                            $service['phone_number'] = $groomerDetails[0]['phone_number'];
                            $service['template_id'] = 'groomerreview/groomerreviewemail/groomerreview_send_center_email';
                            $this->helper->sendMail($service ,$data);
                        }
                    }
                }else{
                    $this->messageManager->addError('Something went wrong while saving the Professionals review.');
                }
            } else {
                $this->messageManager->addError('Something went wrong while saving the Professionals review.');
            }
        }else{
            $this->messageManager->addError('Login customer only can review the Professionals');
        }
        return $resultRedirect;
    }

    public function inputvalidation($data)
    {
        if($data['star_rating'] == "")
        {
            $this->messageManager->addError('Select the star ratings and try again.');
            return 'false';
        }
    }
}