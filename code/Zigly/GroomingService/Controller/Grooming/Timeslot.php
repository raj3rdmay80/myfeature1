<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Controller\Grooming;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Zigly\GroomingService\Model\Session;

class Timeslot extends \Magento\Framework\App\Action\Action
{

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Session
     */
    protected $groomingSession;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param JsonFactory $jsonResultFactory
     * @param PageFactory $resultPageFactory
     * @param Session $groomingSession
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        PageFactory $resultPageFactory,
        Session $groomingSession
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->groomingSession = $groomingSession;
        parent::__construct($context);
    }

    /**
     * Execute save action
     * @return void
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $result = $this->jsonResultFactory->create();
        if (!empty($post['selected_date'])) {
            $groomSession = $this->groomingSession->getGroomService();
            $groomSession['selected_date'] = $post['selected_date'];
            $groomSession['selected_time'] = $post['selected_time'];
            $selectedDatetime = \DateTime::createFromFormat("Y-m-d h:i a", $post['selected_date']." ".$post['selected_time']);
            $groomSession['selected_timestamp'] = $selectedDatetime->getTimestamp();
            $this->groomingSession->setGroomService($groomSession);
        }
        /*if (!empty($post['planid'])) {
            $groomSession = $this->groomingSession->getGroomService();
            $groomSession['planid'] = $post['planid'];
            $groomSession['activity'] = $post['activity'];
            $groomSession['subtotal'] = '';
            $groomSession['grand_total'] = '';
            $groomSession['discount_amount'] = '';
            $groomSession['coupon_code'] = '';
            $this->groomingSession->setGroomService($groomSession);
        }*/
        return $result;
    }
}