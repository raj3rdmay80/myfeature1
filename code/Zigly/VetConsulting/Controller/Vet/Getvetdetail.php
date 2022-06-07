<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Controller\Vet;

use Magento\Framework\App\Action\Context;
use Zigly\Groomer\Model\GroomerFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;

class Getvetdetail extends \Magento\Framework\App\Action\Action
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
     * @param GroomerFactory $groomerFactory
     * @param JsonFactory $jsonResultFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        GroomerFactory $groomerFactory,
        JsonFactory $jsonResultFactory,
        PageFactory $resultPageFactory
    ) {
        $this->groomerFactory = $groomerFactory;
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
        $responseData = [
            'success' => true,
            'message' => "success"
        ];
        $groomer = '';
        if (!empty($post['key'])) {
            $groomer = $this->groomerFactory->create()->load($post['key']);
        }
        $block = $resultPage->getLayout()
                ->createBlock('Magento\Framework\View\Element\Template')
                ->setTemplate('Zigly_VetConsulting::vet/vetdetail.phtml')
                ->setData('vetData', $groomer)
                ->toHtml();

        $result->setData(['output' => $block]);
        // $result->setData($responseData);
        return $result;
    }
}