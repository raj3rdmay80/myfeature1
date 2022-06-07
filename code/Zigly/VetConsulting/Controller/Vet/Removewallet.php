<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Controller\Vet;

use Zigly\VetConsulting\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class Removewallet extends \Magento\Framework\App\Action\Action
{

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var Session
     */
    protected $vetSession;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param Session $vetSession
     * @param JsonFactory $jsonResultFactory
     */
    public function __construct(
        Context $context,
        Session $vetSession,
        JsonFactory $jsonResultFactory
    ) {
        $this->vetSession = $vetSession;
        $this->jsonResultFactory = $jsonResultFactory;
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
        $responseData = [
            'success' => false,
            'message' => 'Something went wrong. Please try again later.'
        ];
        try {
            $consultSession = $this->vetSession->getVet();
            $responseData['success'] = true;
            $responseData['message'] = "Removed successfully.";
            $consultSession['discount_amount'] = '';
            $consultSession['wallet'] = 1;
            $consultSession['wallet_amount'] = '';
            $subtotal = $consultSession['subtotal'];
            if ($consultSession['coupon_discount_amount'] > 0) {
                $subtotal = $subtotal - $consultSession['coupon_discount_amount'];
            }
            $consultSession['grand_total'] = $subtotal;
            $consultSession['wallet_discount_amount'] = '';
            $this->vetSession->setVet($consultSession);
        } catch (\Exception $e) {
            $responseData['trace'] = $e->getMessage();
        }
        $result->setData($responseData);
        return $result;
    }
}