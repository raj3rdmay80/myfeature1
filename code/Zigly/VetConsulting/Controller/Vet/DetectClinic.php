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
use Zigly\VetConsulting\Model\Session;
use Zigly\Groomer\Model\ResourceModel\Groomer\CollectionFactory as GroomerCollectionFactory;
use Zigly\VetConsulting\Model\SessionFactory as VetSession;

class DetectClinic extends \Magento\Framework\App\Action\Action
{

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var CouponCollection
     */
    protected $couponCollection;

    /**
     * @var Session
     */
    protected $vetSession;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param JsonFactory $jsonResultFactory
     * @param CouponCollection $couponCollection
     * @param Session $vetSession
     */
    public function __construct(
        Context $context,
        GroomerCollectionFactory $groomerCollectionFactory,
        JsonFactory $jsonResultFactory,
        PageFactory $resultPageFactory,
        VetSession $vetSession
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->groomerCollectionFactory = $groomerCollectionFactory;
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
        $city = $post['city'];
        $place = $post['place'];
        if ($city == 'नई दिल्ली' || $city == 'New Delhi') {
            $city = 'Delhi';
        }
        $responseData = [
            'status' => false,
            'message' => 'Vet clinic is not available for your location.'
        ];
        try {
            $vetProfessionals = $this->groomerCollectionFactory->create()->addFieldToFilter('professional_role', '3')->addFieldToFilter('status', '1')->addFieldToFilter('city', $city);
            $vetProfessionals->getData();
            if (count($vetProfessionals)) {
                $consultSession = $this->vetSession->create()->getVet();
                if (empty($consultSession)) {
                    $consultSession = [];
                }
                $consultSession['detected_clinic'] = $city;
                $consultSession['detected_place'] = $place;
                $this->vetSession->create()->setVet($consultSession);
                 $resultPage = $this->resultPageFactory->create();
                $block = $resultPage->getLayout()
                    ->createBlock('Zigly\VetConsulting\Block\Vet\Consulting')
                    ->setTemplate('Zigly_VetConsulting::vet/pets.phtml')
                    ->toHtml();
                $responseData['status'] = true;
                $responseData['message'] = true;
                $responseData['output'] = $block;
            } else {
                $responseData['message'] = 'Vet clinic is not available for your location.';
            }
        } catch (\Exception $e) {
            $responseData['message'] = 'Vet clinic is not available for your location.';
            $responseData['trace'] = $e->getMessage();
        }
        
        $result->setData($responseData);
        return $result;
    }
}
