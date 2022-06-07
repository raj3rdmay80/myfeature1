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
        // $resultPage = $this->resultPageFactory->create();
        $responseData = [
            'success' => false,
            'message' => 'Invalid coupon code.'
        ];
        try {
            if (!empty($post['couponcode'])) {
                $groomSession = $this->groomingSession->getGroomService();
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
                    $groomSession['coupon_amount'] = $couponData[0]['amount'];
                    $groomSession['coupon_description'] = $couponData[0]['description'];
                    $groomSession['coupon_code'] = $post['couponcode'];
                    $subtotal = $groomSession['subtotal'];
                    if ($groomSession['wallet_amount'] > 0) {
                        $subtotal = $subtotal - $groomSession['wallet_amount'];
                    }
                    $couponAmount = $couponData[0]['amount'];
                    $groomSession['discount_amount'] = ($subtotal > $couponAmount) ? $couponAmount : $subtotal;
                    $groomSession['coupon_discount_amount'] = ($subtotal > $couponAmount) ? $couponAmount : $subtotal;
                    $grandTotal = $subtotal - $couponData[0]['amount'];
                    $groomSession['grand_total'] = $grandTotal > 0 ? $grandTotal : 0;
                    $groomSession['coupon'] =  $grandTotal > 0 ? $grandTotal : 0;
                    if ($groomSession['wallet_amount'] > 0) {
                        $groomSession['coupon'] =  1;
                    }
                    $this->groomingSession->setGroomService($groomSession);
                }
            }
        } catch (\Exception $e) {
            $responseData['trace'] = $e->getMessage();
        }
        
        $result->setData($responseData);

        return $result;
    }
}
