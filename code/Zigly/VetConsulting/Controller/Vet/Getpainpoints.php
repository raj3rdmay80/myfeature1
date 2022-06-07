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

class Getpainpoints extends \Magento\Framework\App\Action\Action
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
     * @param Context  $context
     * @param vetSession $vetSession
     * @param JsonFactory $jsonResultFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        VetSession $vetSession,
        JsonFactory $jsonResultFactory,
        PageFactory $resultPageFactory
    ) {
        $this->vetSession = $vetSession;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->resultPageFactory = $resultPageFactory;
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
        $resultPage = $this->resultPageFactory->create();
        if (!empty($post['petid'])) {
            $consultSession = $this->vetSession->getVet();
            $consultSession['pet_id'] = $post['petid'];
            $consultSession['pain_points'] = '';
            $consultSession['pain_description'] = '';
            $consultSession['image_document'] = '';
            $this->vetSession->setVet($consultSession);
        }
        $block = $resultPage->getLayout()
                ->createBlock('Zigly\VetConsulting\Block\Vet\PainPoints')
                ->setTemplate('Zigly_VetConsulting::vet/painpoints.phtml')
                ->toHtml();
                /*->setData('petData', $post)*/
        $result->setData(['output' => $block]);
        return $result;
    }
}