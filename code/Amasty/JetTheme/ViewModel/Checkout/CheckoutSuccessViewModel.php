<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;

class CheckoutSuccessViewModel implements ArgumentInterface
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var AddressRenderer
     */
    private $addressRenderer;

    public function __construct(
        Session $checkoutSession,
        AddressRenderer $addressRenderer
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->addressRenderer = $addressRenderer;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->checkoutSession->getLastRealOrder();
    }

    /**
     * @param Address $address
     * @param string $format
     * @return string|null
     */
    public function getFormattedAddress(Address $address, string $format = 'oneline'): ?string
    {
        return $this->addressRenderer->format($address, $format);
    }

    /**
     * @return bool
     */
    public function isPaypalBillingAgreement(): bool
    {
        return (bool)$this->checkoutSession->getLastBillingAgreementReferenceId();
    }

    /**
     * fix for magento 2.3.0, there is no customer names in order object
     *
     * @param Order $order
     * @return void
     */
    public function fixOrderCustomerNames(Order $order): void
    {
        if ($order->getCustomerFirstname()) {
            return;
        }

        $billingAddress = $order->getBillingAddress();
        $order->setCustomerFirstname($billingAddress->getFirstname());
        $order->setCustomerLastname($billingAddress->getLastname());
        $order->setCustomerMiddlename($billingAddress->getMiddlename());
    }
}
