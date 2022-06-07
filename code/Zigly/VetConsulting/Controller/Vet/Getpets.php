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
use Zigly\VetConsulting\Model\SessionFactory as VetSession;

class Getpets extends \Magento\Framework\App\Action\Action
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
    protected $vetSession;

    /**
     * Constructor
     * @param Context  $context
     * @param JsonFactory $jsonResultFactory
     * @param PageFactory $resultPageFactory
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
        /*$post = $this->getRequest()->getPostValue();*/
        $result = $this->jsonResultFactory->create();
        $resultPage = $this->resultPageFactory->create();
        $responseData = [
            'status' => false,
            'message' => 'Select your location.'
        ];
        $consultSession = $this->vetSession->create()->getVet();
        if (!empty($consultSession['detected_clinic'])) {
            $block = $resultPage->getLayout()
                    ->createBlock('Zigly\VetConsulting\Block\Vet\Consulting')
                    ->setTemplate('Zigly_VetConsulting::vet/pets.phtml')
                    ->toHtml();
            $responseData['status'] = true;
            $responseData['message'] = false;
            $responseData['output'] = $block;
        }

                /*->setData('petData', $post)*/
        $result->setData($responseData);
        return $result;
    }
}