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
        JsonFactory $jsonResultFactory,
        CouponCollection $couponCollection,
        Session $vetSession
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->couponCollection = $couponCollection;
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
        $responseData = [
            'success' => false,
            'message' => 'Something went wrong. Please try again later.'
        ];
        try {
            $consultSession = $this->vetSession->getVet();
            $responseData['success'] = true;
            $responseData['message'] = "Removed successfully.";
            $consultSession['coupon_code'] = '';
            $consultSession['coupon_amount'] = '';
            $consultSession['coupon_description'] = '';
            $consultSession['coupon'] = 1;
            $consultSession['discount_amount'] = '';
            $subtotal = $consultSession['subtotal'];
            if ($consultSession['wallet_amount'] > 0) {
                $subtotal = $subtotal - $consultSession['wallet_amount'];
            }
            $consultSession['grand_total'] = $subtotal;
            $consultSession['coupon_discount_amount'] = '';
            $this->vetSession->setVet($consultSession);
        } catch (\Exception $e) {
            $responseData['trace'] = $e->getMessage();
        }
        $result->setData($responseData);
        return $result;
    }
}