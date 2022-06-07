<?php
/**
 * Copyright (C) 2021  Zigly
 * @package  Zigly_Login
 */

declare(strict_types=1);

namespace Zigly\Login\Controller\Checkout;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class Index extends \Magento\Checkout\Controller\Onepage implements HttpGetActionInterface
{
    /**
     * Checkout page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Checkout\Helper\Data $checkoutHelper */
        $checkoutHelper = $this->_objectManager->get(\Magento\Checkout\Helper\Data::class);
        if (!$checkoutHelper->canOnepageCheckout()) {
            $this->messageManager->addErrorMessage(__('One-page checkout is turned off.'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        if (!$this->_customerSession->isLoggedIn() && !$checkoutHelper->isAllowedGuestCheckout($quote)) {
            // $this->messageManager->addErrorMessage(__('Guest checkout is disabled.'));
            $urlInterface = $this->_objectManager->get(\Magento\Framework\UrlInterface::class);
            // $this->_customerSession->setAfterAuthUrl( $this->_objectManager->get(\Magento\Framework\UrlInterface::class)->getCurrentUrl());
            // $this->_customerSession->authenticate();
            // $login_url = $urlInterface->getUrl('customer/account/login', array('referer' => base64_encode($urlInterface->getCurrentUrl())));
            // return $this->resultRedirectFactory->create()->setUrl($login_url);
            $cookieManager = $this->_objectManager->get(\Magento\Framework\Stdlib\CookieManagerInterface::class);
            $cookieMetadataFactory = $this->_objectManager->get(\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class);
            $publicCookieMetadata = $cookieMetadataFactory->createPublicCookieMetadata();
            $publicCookieMetadata->setDuration('900');
            $publicCookieMetadata->setPath('/');
            $publicCookieMetadata->setHttpOnly(false);
            $cookieManager->setPublicCookie('afterloginurl', $urlInterface->getCurrentUrl(), $publicCookieMetadata);
            return $this->resultRedirectFactory->create()->setPath('customer/account/login');

        }

        // generate session ID only if connection is unsecure according to issues in session_regenerate_id function.
        // @see http://php.net/manual/en/function.session-regenerate-id.php
        if (!$this->isSecureRequest()) {
            $this->_customerSession->regenerateId();
        }
        $this->_objectManager->get(\Magento\Checkout\Model\Session::class)->setCartWasUpdated(false);
        $this->getOnepage()->initCheckout();
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Checkout'));
        return $resultPage;
    }

    /**
     * Checks if current request uses SSL and referer also is secure.
     *
     * @return bool
     */
    private function isSecureRequest(): bool
    {
        $request = $this->getRequest();

        $referrer = $request->getHeader('referer');
        $secure = false;

        if ($referrer) {
            $scheme = parse_url($referrer, PHP_URL_SCHEME);
            $secure = $scheme === 'https';
        }

        return $secure && $request->isSecure();
    }
}
