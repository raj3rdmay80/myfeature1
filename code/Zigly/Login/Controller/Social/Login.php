<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */

namespace Zigly\Login\Controller\Social;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;

/**
 * Class Login
 *
 * @package Zigly\Login\Controller\Social
 */
class Login extends \Mageplaza\SocialLogin\Controller\Social\AbstractSocial
{
    /**
     * @return ResponseInterface|Raw|ResultInterface|Login|void
     * @throws FailureToSendException
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute()
    {
        if ($this->checkCustomerLogin() && $this->session->isLoggedIn()) {
            $this->_redirect('customer/account');

            return;
        }

        $type = $this->apiHelper->setType($this->getRequest()->getParam('type'));

        if (!$type) {
            $this->_forward('noroute');

            return;
        }

        try {
            $userProfile = $this->apiObject->getUserProfile($type);

            if (!$userProfile->identifier) {
                return $this->emailRedirect($type);
            }
        } catch (Exception $e) {
            $this->setBodyResponse($e->getMessage());

            return;
        }

        $customer     = $this->apiObject->getCustomerBySocial($userProfile->identifier, $type);
        $customerData = $this->customerModel->load($customer->getId());
        if ($customer->getId() && $customer->getIsApproved() != 'approved') {
            $this->messageManager->addErrorMessage(__('Your are not approved.'));
            return $this->_appendJs(
                "<script>window.close();window.opener.location.reload(true);</script>"
            );
        }

        if (!$customer->getId()) {
            $requiredMoreInfo = (int)$this->apiHelper->requiredMoreInfo();

            if ((!$userProfile->email && $requiredMoreInfo === 2) || $requiredMoreInfo === 1) {
                $this->session->setUserProfile($userProfile);

                return $this->_appendJs(
                    sprintf(
                        "<script>window.close();window.opener.fakeEmailCallback('%s','%s','%s');</script>",
                        $type,
                        $userProfile->firstName,
                        $userProfile->lastName
                    )
                );
            }

            $customer = $this->createCustomerProcess($userProfile, $type);
        } elseif ($this->apiHelper->isCheckMode()) {
            if ($customerData->getData('password_hash') === null) {
                $userProfile->hash = '';
                $this->session->setUserProfile($userProfile);

                return $this->_appendJs(
                    sprintf(
                        "<script>window.close();window.opener.fakeEmailCallback('%s','%s','%s');</script>",
                        $type,
                        $userProfile->firstName,
                        $userProfile->lastName
                    )
                );
            }

            $userProfile->hash = $customerData->getData('password_hash');
            $this->session->setUserProfile($userProfile);

            return $this->_appendJs(
                sprintf(
                    "<script>window.close();window.opener.fakeEmailCallback('%s','%s','%s');</script>",
                    $type,
                    $userProfile->firstName,
                    $userProfile->lastName
                )
            );
        }
        $this->refresh($customer);

        return $this->_appendJs();
    }

    /**
     * @return bool
     */
    public function checkCustomerLogin()
    {
        return true;
    }

    /**
     * @param $message
     */
    protected function setBodyResponse($message)
    {
        $content = '<html><head></head><body>';
        $content .= '<div class="message message-error">' . __('Ooophs, we got an error: %1', $message) . '</div>';
        $content .= <<<Style
<style type="text/css">
    .message{
        background: #fffbbb;
        border: none;
        border-radius: 0;
        color: #333333;
        font-size: 1.4rem;
        margin: 0 0 10px;
        padding: 1.8rem 4rem 1.8rem 1.8rem;
        position: relative;
        text-shadow: none;
    }
    .message-error{
        background:#ffcccc;
    }
</style>
Style;
        $content .= '</body></html>';
        $this->getResponse()->setBody($content);
    }
}
