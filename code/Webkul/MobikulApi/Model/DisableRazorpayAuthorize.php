<?php

namespace Webkul\MobikulApi\Model;

use Magento\Payment\Model\InfoInterface;

class DisableRazorpayAuthorize extends \Razorpay\Magento\Model\PaymentMethod
{
    public function authorize(InfoInterface $payment,$amount)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->create(\Magento\Framework\App\RequestInterface::class);
        $routeName = $request->getRouteName();
        if (strpos($_SERVER['REQUEST_URI'],'mobikulhttp') !== false || strpos($_SERVER['REQUEST_URI'],'mobikulmphttp') !== false) {
                return $this;
        } else {
            parent::authorize($payment, $amount);
        }
    }

    public function capture(InfoInterface $payment, $amount)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->create(\Magento\Framework\App\RequestInterface::class);
        $routeName = $request->getRouteName();
        if (strpos($_SERVER['REQUEST_URI'],'mobikulhttp') !== false || strpos($_SERVER['REQUEST_URI'],'mobikulmphttp') !== false) {
                return $this;
        } else {
            parent::capture($payment, $amount);
        }
    }

    public function getAmountPaid($paymentId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->create(\Magento\Framework\App\RequestInterface::class);
        $checkoutSession = $objectManager->create(\Magento\Checkout\Model\Session::class);
        $routeName = $request->getRouteName();
        if (strpos($_SERVER['REQUEST_URI'],'mobikulhttp') !== false || strpos($_SERVER['REQUEST_URI'],'mobikulmphttp') !== false) {
                $amount = $checkoutSession->getOrderAmount();
        } else {
            $amount = parent::getAmountPaid($paymentId);
        }
        return $amount;
    }
}
