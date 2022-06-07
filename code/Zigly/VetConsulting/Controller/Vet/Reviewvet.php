<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Controller\Vet;

use Magento\Framework\App\Action\Context;
use Zigly\Groomer\Model\GroomerFactory as ProfessionalFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Zigly\VetConsulting\Model\Session as VetSession;

class Reviewvet extends \Magento\Framework\App\Action\Action
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
     * @param ProfessionalFactory $professionalFactory
     */
    public function __construct(
        Context $context,
        VetSession $vetSession,
        JsonFactory $jsonResultFactory,
        PageFactory $resultPageFactory,
        ProfessionalFactory $professionalFactory
    ) {
        $this->vetSession = $vetSession;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->professionalFactory = $professionalFactory;
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
            'output' => ""
        ];
        if (!empty($post['vet_id'])) {
            $professional = $this->professionalFactory->create()->load($post['vet_id']);
            if (!empty($professional)) {
                $consultSession = $this->vetSession->getVet();
                $consultSession['vet_id'] = $post['vet_id'];
                $consultSession['schedule_id'] = !empty($post['schedule_id']) ? $post['schedule_id'] : '';
                $consultSession['street'] = $professional->getAddressLine();
                $consultSession['region'] = $professional->getState(); //TODO: state name
                $consultSession['region_id'] = $professional->getState();
                $consultSession['city'] = $professional->getCity();
                $consultSession['postcode'] = $professional->getPincode();
                $consultSession['subtotal'] = $professional->getForVetConsulting();
                $consultSession['grand_total'] = $professional->getForVetConsulting();
                $consultSession['discount_amount'] = '';
                $consultSession['wallet_amount'] = '';
                $consultSession['wallet_balance'] = '';
                $consultSession['wallet_discount_amount'] = '';
                $consultSession['coupon_discount_amount'] = '';
                $consultSession['wallet'] = 1;
                $consultSession['coupon'] = 1;
                $consultSession['coupon_code'] = '';
                $consultSession['coupon_amount'] = '';
                $consultSession['coupon_description'] = '';
                $this->vetSession->setVet($consultSession);
                $block = $resultPage->getLayout()
                        ->createBlock('Zigly\VetConsulting\Block\Vet\Review')
                        ->setTemplate('Zigly_VetConsulting::vet/review.phtml')
                        ->setData('vetData', $post)
                        ->toHtml();
                $responseData = [
                    'success' => true,
                    'output' => $block
                ];
            } else {
                $responseData = [
                    'success' => true,
                    'output' => "<p>Something went wrong.</p>"
                ];
            }
        }
        $result->setData($responseData);
        return $result;
    }
}