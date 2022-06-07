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

class Loadreview extends \Magento\Framework\App\Action\Action
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
     * Constructor
     * @param Context  $context
     * @param JsonFactory $jsonResultFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        PageFactory $resultPageFactory
    ) {
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
        $block = $resultPage->getLayout()
                    ->createBlock('Zigly\VetConsulting\Block\Vet\Review')
                    ->setTemplate('Zigly_VetConsulting::vet/review.phtml')
                    ->setData('vetData', $post)
                    ->toHtml();
        $result->setData(['output' => $block]);
        return $result;
    }
}