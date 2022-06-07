<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Controller\Vet;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Zigly\VetConsulting\Model\Session as VetSession;

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
     * @var VetSession
     */
    protected $vetSession;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param JsonFactory $jsonResultFactory
     * @param PageFactory $resultPageFactory
     * @param VetSession $vetSession
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        PageFactory $resultPageFactory,
        VetSession $vetSession
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->vetSession = $vetSession;
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
            $consultSession = $this->vetSession->getVet();
            $consultSession['selected_date'] = $post['selected_date'];
            $consultSession['selected_time'] = $post['selected_time'];
            $selectedDatetime = \DateTime::createFromFormat("Y-m-d h:i a", $post['selected_date']." ".$post['selected_time']);
            $consultSession['selected_timestamp'] = $selectedDatetime->getTimestamp();
            $this->vetSession->setVet($consultSession);
        } else {
            $consultSession = $this->vetSession->getVet();
            $consultSession['selected_date'] = '';
            $consultSession['selected_time'] = '';
            $consultSession['selected_timestamp'] = '';
            $this->vetSession->setVet($consultSession);
        }
        return $result;
    }
}