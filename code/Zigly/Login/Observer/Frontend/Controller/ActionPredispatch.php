<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
declare(strict_types=1);

namespace Zigly\Login\Observer\Frontend\Controller;

use Magento\Framework\UrlInterface;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\ActionFlag;
use \Magento\Framework\Exception\NotFoundException;
use Magento\LoginAsCustomerApi\Api\ConfigInterface;
use Magento\LoginAsCustomerApi\Api\GetLoggedAsCustomerAdminIdInterface;
use Magento\LoginAsCustomerApi\Api\IsLoginAsCustomerSessionActiveInterface;

class ActionPredispatch implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var $encryptor
     */
    protected $encryptor;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var $urlInterface
     */
    protected $urlInterface;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var IsLoginAsCustomerSessionActiveInterface
     */
    private $isLoginAsCustomerSessionActive;

    /**
     * @var GetLoggedAsCustomerAdminIdInterface
     */
    private $getLoggedAsCustomerAdminId;

    /**
     * @param Encryptor $encryptor
     * @param UrlInterface $urlInterface
     * @param CustomerSession $customerSession
     * @param ManagerInterface $messageManager
     * @param ConfigInterface $config
     * @param IsLoginAsCustomerSessionActiveInterface $isLoginAsCustomerSessionActive
     * @param GetLoggedAsCustomerAdminIdInterface $getLoggedAsCustomerAdminId
    */
    public function __construct(
        Encryptor $encryptor,
        UrlInterface $urlInterface,
        CustomerSession $customerSession,
        ManagerInterface $messageManager,
        ConfigInterface $config,
        IsLoginAsCustomerSessionActiveInterface $isLoginAsCustomerSessionActive,
        GetLoggedAsCustomerAdminIdInterface $getLoggedAsCustomerAdminId,
        ActionFlag $actionFlag
    ){
        $this->encryptor = $encryptor;
        $this->urlInterface = $urlInterface;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->actionFlag = $actionFlag;
        $this->isLoginAsCustomerSessionActive = $isLoginAsCustomerSessionActive;
        $this->config = $config;
        $this->getLoggedAsCustomerAdminId = $getLoggedAsCustomerAdminId;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $actionName = $observer->getEvent()->getRequest()->getFullActionName();
        $adminId = $this->getLoggedAsCustomerAdminId->execute();
        $customerId = (int)$this->customerSession->getCustomerId();
        if ($this->config->isEnabled() && $adminId && $customerId) {
            if ($this->isLoginAsCustomerSessionActive->execute($customerId, $adminId)) {
                if ($actionName == 'customer_account_edit') {
                     throw new NotFoundException(__('Not allowed.'));
                }
            }
        }

        $urlInterface = $this->urlInterface;
        $actionArray = [
            'customer_account_edit',
            'customer_account_logout',
            'customer_section_load',
            'login_verify_verifyotp',
            'login_verify_sendotp',
            'login_verify_resendotp'
        ];

        if ($this->customerSession->isLoggedIn() && !in_array($actionName, $actionArray)) {
            $customer = $this->customerSession->getCustomer();
            if ($customer->getData('is_approved') != 'approved') {
                $this->customerSession->logout();
                $url = $urlInterface->getUrl();
                $observer->getControllerAction()->getResponse()->setRedirect($url);
            } else if (empty($customer->getData('phone_number'))) {
                $url = $urlInterface->getUrl('customer/account/edit/required/true');
                /* $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);*/
                $observer->getControllerAction()->getResponse()->setRedirect($url);
            }
        }
        if($actionName == 'customer_account_forgotpassword'){
            $url = $urlInterface->getUrl('customer/account/login');
            $observer->getControllerAction()->getResponse()->setRedirect($url);
        }
    }
}
