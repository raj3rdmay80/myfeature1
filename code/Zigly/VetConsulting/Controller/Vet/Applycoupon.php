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

class Applycoupon extends \Magento\Framework\App\Action\Action
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
            'message' => 'Invalid coupon code.'
        ];
        try {
            if (!empty($post['couponcode'])) {
                $consultSession = $this->vetSession->getVet();
                $now = new \DateTime();
                $couponData = $this->couponCollection->create()
                    ->addFieldToFilter('coupon_code', $post['couponcode'])
                    ->addFieldToFilter('start_date', ['lteq' => $now->format('Y-m-d H:i:s')])
                    ->addFieldToFilter('end_date', ['gteq' => $now->format('Y-m-d H:i:s')])
                    ->addFieldToFilter('type', 2)
                    ->addFieldToFilter('center', 1)
                    ->addFieldToFilter('status', 1)->getData();
                if (count($couponData)) {
                    $responseData['success'] = true;
                    $responseData['amount'] = $couponData[0]['amount'];
                    $responseData['message'] = "Successfully applied.";
                    $consultSession['coupon_amount'] = $couponData[0]['amount'];
                    $consultSession['coupon_description'] = $couponData[0]['description'];
                    $consultSession['coupon_code'] = $post['couponcode'];
                    $subtotal = $consultSession['subtotal'];
                    if ($consultSession['wallet_amount'] > 0) {
                        $subtotal = $subtotal - $consultSession['wallet_amount'];
                    }
                    $couponAmount = $couponData[0]['amount'];
                    $consultSession['discount_amount'] = ($subtotal > $couponAmount) ? $couponAmount : $subtotal;
                    $consultSession['coupon_discount_amount'] = ($subtotal > $couponAmount) ? $couponAmount : $subtotal;
                    $grandTotal = $subtotal - $couponData[0]['amount'];
                    $consultSession['grand_total'] = $grandTotal > 0 ? $grandTotal : 0;
                    $consultSession['coupon'] =  $grandTotal > 0 ? $grandTotal : 0;
                    if ($consultSession['wallet_amount'] > 0) {
                        $consultSession['coupon'] =  1;
                    }
                    $this->vetSession->setVet($consultSession);
                }
            }
        } catch (\Exception $e) {
            $responseData['trace'] = $e->getMessage();
        }
        $result->setData($responseData);
        return $result;
    }
}
