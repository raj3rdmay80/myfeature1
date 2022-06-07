<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Controller\Experiencegrooming;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Zigly\GroomingService\Model\Session;
use Zigly\Hub\Model\HubFactory;

class Review extends \Magento\Framework\App\Action\Action
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
     * @var HubFactory
     */
    protected $hub;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param HubFactory $hub
     * @param JsonFactory $jsonResultFactory
     * @param PageFactory $resultPageFactory
     * @param Session $groomingSession
     */
    public function __construct(
        Context $context,
        HubFactory $hub,
        JsonFactory $jsonResultFactory,
        PageFactory $resultPageFactory,
        Session $groomingSession
    ) {
        $this->hub = $hub;
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
        $resultPage = $this->resultPageFactory->create();
        $address = '';
        $groomSession = $this->groomingSession->getGroomCenter();
        if (!empty($post['address_id'])) {
            $groomSession['address_id'] = $post['address_id'];
            $this->groomingSession->setGroomCenter($groomSession);
        }
        if (!empty($groomSession['address_id'])) {
            $shippingAddress = $this->hub->create()->load($groomSession['address_id']);
            $address = $shippingAddress->getLocation();
        }

        $block = $resultPage->getLayout()
                ->createBlock('Zigly\GroomingService\Block\ExperienceGrooming\Review')
                ->setTemplate('Zigly_GroomingService::experiencegrooming/review.phtml')
                ->setData('petData', $post)
                ->toHtml();

        $result->setData(['output' => $block, 'address' => $address]);

        return $result;
    }
}