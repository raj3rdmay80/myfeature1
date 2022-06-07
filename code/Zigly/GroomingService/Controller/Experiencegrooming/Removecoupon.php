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
use Zigly\CouponService\Model\ResourceModel\CouponService\CollectionFactory as CouponCollection;

class Removecoupon extends \Magento\Framework\App\Action\Action
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
    protected $groomingSession;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param JsonFactory $jsonResultFactory
     * @param CouponCollection $couponCollection
     * @param Session $groomingSession
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        CouponCollection $couponCollection,
        Session $groomingSession
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->couponCollection = $couponCollection;
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
        $responseData = [
            'success' => false,
            'message' => 'Something went wrong. Please try again later.'
        ];
        try {
            $groomSession = $this->groomingSession->getGroomCenter();
            $responseData['success'] = true;
            $responseData['message'] = "Removed successfully.";
            $groomSession['coupon_code'] = '';
            $groomSession['coupon_amount'] = '';
            $groomSession['coupon_description'] = '';
            $groomSession['coupon'] = 1;
            $groomSession['coupon_discount_amount'] = '';
            $groomSession['discount_amount'] = '';
            $subtotal = $groomSession['subtotal'];
            if ($groomSession['wallet_amount'] > 0) {
                $subtotal = $subtotal - $groomSession['wallet_amount'];
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