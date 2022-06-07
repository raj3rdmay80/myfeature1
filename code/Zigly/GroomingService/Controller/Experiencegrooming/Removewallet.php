<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Controller\Experiencegrooming;

use Zigly\GroomingService\Model\Session;
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
    protected $groomingSession;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param Session $groomingSession
     * @param JsonFactory $jsonResultFactory
     */
    public function __construct(
        Context $context,
        Session $groomingSession,
        JsonFactory $jsonResultFactory
    ) {
        $this->groomingSession = $groomingSession;
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
            $groomSession = $this->groomingSession->getGroomCenter();
            $responseData['success'] = true;
            $responseData['message'] = "Removed successfully.";
            $groomSession['discount_amount'] = '';
            $groomSession['wallet_discount_amount'] = '';
            $groomSession['wallet_amount'] = '';
            $groomSession['wallet'] = 1;
            $subtotal = $groomSession['subtotal'];
            if ($groomSession['coupon_discount_amount'] > 0) {
                $subtotal = $subtotal - $groomSession['coupon_discount_amount'];
            }
            $groomSession['grand_total'] = $subtotal;
            $this->groomingSession->setGroomCenter($groomSession);
        } catch (\Exception $e) {
            $responseData['trace'] = $e->getMessage();
        }
        
        $result->setData($responseData);

        return $result;
    }
}