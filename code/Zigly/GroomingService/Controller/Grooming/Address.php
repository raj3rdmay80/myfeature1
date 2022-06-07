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
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Address extends \Magento\Framework\App\Action\Action
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
     * @param TimezoneInterface $timezoneInterface
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        PageFactory $resultPageFactory,
        TimezoneInterface $timezoneInterface,
        Session $groomingSession
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->groomingSession = $groomingSession;
        $this->timezoneInterface = $timezoneInterface;
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
        // if (!empty($post['address_id'])) {
        //     $groomSession = $this->groomingSession->getGroomService();
        //     $groomSession['address_id'] = $post['address_id'];
        //     $this->groomingSession->setGroomService($groomSession);
        // }
        $groomSession = $this->groomingSession->getGroomService();
        $block = $resultPage->getLayout()
                ->createBlock('Zigly\GroomingService\Block\Grooming\Index')
                ->setTemplate('Zigly_GroomingService::grooming/addresslist.phtml')
                ->setData('petData', $post)
                ->toHtml();

        $result->setData([
            'output' => $block,
            'date' => $this->timezoneInterface->date(new \DateTime($groomSession['selected_date']))->format('d M \'y'),
            'time' => $groomSession['selected_time']
        ]);

        return $result;
    }
}