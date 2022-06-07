<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Controller\Checkout;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Customer\Model\SessionFactory as CustomerSession;
use Magento\Framework\Serialize\SerializerInterface;

class ToggleWallet extends \Magento\Framework\App\Action\Action
{
// wallet/wallet_usage/max_transaction 

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
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param CustomerSession $customerSession
     * @param JsonFactory $jsonResultFactory
     * @param CartRepositoryInterface $quoteRepository
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        CustomerSession $customerSession,
        CartRepositoryInterface $quoteRepository,
        SerializerInterface $serializer,
        CheckoutSession $checkoutSession
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->serializer = $serializer;
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
            'message' => 'Something went wrong.'
        ];
        try {
            $cartId = $this->checkoutSession->getQuote()->getId();

            $quote = $this->quoteRepository->get((int)$cartId);
            $quote->getShippingAddress()->setCollectShippingRates(true);

            if (!empty($quote->getZwallet())) {
                $zwallet = $this->serializer->unserialize($quote->getZwallet());
            } else {
                $zwallet = [];
            }


            // $customer = $this->customerSession->create()->getCustomer();
            // $totalBalance = is_null($customer->getWalletBalance()) ? "0" : $customer->getWalletBalance();

            if (isset($post['is_checked']) && $post['is_checked'] == 'true') {
                $zwallet['applied'] = true;
            } elseif (isset($post['is_checked']) && $post['is_checked'] == 'false') {
                $zwallet['applied'] = false;
            }
            $zwallet = $this->serializer->serialize($zwallet);
            $quote->setZwallet($zwallet);

            $quote->setDataChanges(true);
            $quote->collectTotals();
            $this->quoteRepository->save($quote);

            $responseData['success'] = true;
            $responseData['message'] = "Successfully applied.";
        } catch (\Exception $e) {
            $responseData['trace'] = $e->getMessage();
        }

        $result->setData($responseData);


        return $result;
        // print_r($post);
        // exit();

        // $resultPage = $this->resultPageFactory->create();
        
        // try {
        //     if (!empty($post['couponcode'])) {
        //         $groomSession = $this->groomingSession->getGroomService();
        //         $now = new \DateTime();
        //         $couponData = $this->couponCollection->create()
        //             ->addFieldToFilter('coupon_code', $post['couponcode'])
        //             ->addFieldToFilter('start_date', ['lteq' => $now->format('Y-m-d H:i:s')])
        //             ->addFieldToFilter('end_date', ['gteq' => $now->format('Y-m-d H:i:s')])
        //             ->addFieldToFilter('type', 2)
        //             ->addFieldToFilter('center', 1)
        //             ->addFieldToFilter('status', 1)->getData();
        //         if (count($couponData)) {
        //             $responseData['success'] = true;
        //             $responseData['amount'] = $couponData[0]['amount'];
        //             $responseData['message'] = "Successfully applied.";
        //             $groomSession['coupon_code'] = $post['couponcode'];
        //             $subtotal = $groomSession['subtotal'];
        //             if ($groomSession['wallet_amount'] > 0) {
        //                 $subtotal = $subtotal - $groomSession['wallet_amount'];
        //             }
        //             $couponAmount = $couponData[0]['amount'];
        //             $groomSession['discount_amount'] = ($subtotal > $couponAmount) ? $couponAmount : $subtotal;
        //             $groomSession['coupon_discount_amount'] = ($subtotal > $couponAmount) ? $couponAmount : $subtotal;
        //             $grandTotal = $subtotal - $couponData[0]['amount'];
        //             $groomSession['grand_total'] = $grandTotal > 0 ? $grandTotal : 0;
        //             $groomSession['coupon'] =  $grandTotal > 0 ? $grandTotal : 0;
        //             if ($groomSession['wallet_amount'] > 0) {
        //                 $groomSession['coupon'] =  1;
        //             }
        //             $this->groomingSession->setGroomService($groomSession);
        //         }
        //     }
        // } catch (\Exception $e) {
        //     $responseData['trace'] = $e->getMessage();
        // }
        
        
    }
}