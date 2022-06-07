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
use Zigly\Managepets\Model\ManagepetsFactory;

class Getplans extends \Magento\Framework\App\Action\Action
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
     * @var ManagepetsFactory
     */
    protected $pets;

    /**
     * @var ManagepetsFactory
     */
    protected $petName;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param ManagepetsFactory $petsFactory
     * @param JsonFactory $jsonResultFactory
     * @param PageFactory $resultPageFactory
     * @param Session $groomingSession
     */
    public function __construct(
        Context $context,
        ManagepetsFactory $petsFactory,
        JsonFactory $jsonResultFactory,
        PageFactory $resultPageFactory,
        Session $groomingSession
    ) {
        $this->pets = $petsFactory;
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
        if (!empty($post['petid'])) {
            $groomSession = $this->groomingSession->getGroomService();
            if (!empty($groomSession['pet_id']) && ($groomSession['pet_id'] != $post['petid'])) {
                /*unset($groomSession['planid']);*/
                unset($groomSession['activity']);
            }
            $groomSession['pet_id'] = $post['petid'];
            $petName = $this->pets->create()->load($post['petid'])->getName();
            $groomSession['pet_name'] = $petName;
            $this->groomingSession->setGroomService($groomSession);
        }
        $block = $resultPage->getLayout()
                ->createBlock('Zigly\GroomingService\Block\Grooming\Plan')
                ->setTemplate('Zigly_GroomingService::grooming/plans.phtml')
                ->setData('petData', $post)
                ->toHtml();

        $result->setData(['output' => $block, 'pet_name' => $petName]);

        return $result;
    }
}