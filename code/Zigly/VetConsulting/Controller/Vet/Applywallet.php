<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Controller\Vet;

use Magento\Customer\Model\Customer;
use Zigly\VetConsulting\Model\Session;
use Magento\Framework\App\Action\Context;
use Zigly\Wallet\Helper\Data as WalletHelper;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\SessionFactory as CustomerSession;
use Zigly\CouponService\Model\ResourceModel\CouponService\CollectionFactory as CouponCollection;

class Applywallet extends \Magento\Framework\App\Action\Action
{

    /**
     * Customer session model
     *
     * @var CustomerSession
     */
    protected $customerSession;

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
     * @var WalletHelper
     */
    private $walletHelper;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param Customer $customer
     * @param Session $vetSession
     * @param WalletHelper $walletHelper
     * @param JsonFactory $jsonResultFactory
     * @param CustomerSession $customerSession
     * @param CouponCollection $couponCollection
     */
    public function __construct(
        Context $context,
        Customer $customer,
        Session $vetSession,
        WalletHelper $walletHelper,
        JsonFactory $jsonResultFactory,
        CustomerSession $customerSession,
        CouponCollection $couponCollection
    ) {
        $this->customer = $customer;
        $this->vetSession = $vetSession;
        $this->walletHelper = $walletHelper;
        $this->customerSession = $customerSession;
        $this->couponCollection = $couponCollection;
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
            'message' => 'Insufficient balance in wallet money.'
        ];
        try {
            $customer = $this->customerSession->create()->getCustomer();
            $totalBalance = is_null($customer->getWalletBalance()) ? "0" : $customer->getWalletBalance();
            if ($this->walletHelper->isEnabled() && $totalBalance > 1 ) {
                $consultSession = $this->vetSession->getVet();
                $maxTransactionPercent = (!empty($this->walletHelper->getMaxTransactionPercent())) ? (float)$this->walletHelper->getMaxTransactionPercent() : 0;
                $subtotal = floor($consultSession['subtotal']);
                if ($consultSession['coupon_discount_amount'] > 0) {
                    $subtotal = $subtotal - floor($consultSession['coupon_discount_amount']);
                }
                $calculatedWalletApplicable = ($maxTransactionPercent / 100) * $subtotal;
                if ($calculatedWalletApplicable <= 0) {
                    $consultSession['grand_total'] = 0;
                } else {
                    if ($totalBalance <= $calculatedWalletApplicable) {
                        $consultSession['grand_total'] = floor($totalBalance);
                    } else if ($totalBalance > $calculatedWalletApplicable) {
                        $consultSession['grand_total'] = floor($calculatedWalletApplicable);
                    }
                }
                $consultSession['discount_amount'] = ($subtotal > $consultSession['grand_total']) ? $consultSession['grand_total'] : $subtotal;
                $consultSession['wallet_discount_amount'] = ($subtotal > $consultSession['grand_total']) ? $consultSession['grand_total'] : $subtotal;
                $consultSession['wallet_amount'] = ($subtotal > $consultSession['grand_total']) ? $consultSession['grand_total'] : $subtotal;
                $grandTotal = $subtotal - $consultSession['grand_total'];
                $consultSession['grand_total'] =  floor($grandTotal);
                $consultSession['wallet'] =  floor($grandTotal);
                if ($consultSession['coupon_discount_amount'] > 0) {
                    $consultSession['wallet'] =  1;
                }
                $responseData['success'] = true;
                $responseData['amount'] = $grandTotal;
                $responseData['discount'] = $consultSession['wallet_discount_amount'];
                $responseData['message'] = "Successfully applied.";
                $this->vetSession->setVet($consultSession);
            }
        } catch (\Exception $e) {
            $responseData = [
                'success' => false,
                'trace' => $e->getMessage(),
                'message' => $e->getMessage()
            ];
        }
        $result->setData($responseData);
        return $result;
    }
}