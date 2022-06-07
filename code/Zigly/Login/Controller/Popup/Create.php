<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Controller\Popup;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Captcha\Helper\Data as CaptchaData;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Helper\Address;
use Magento\Framework\UrlFactory;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Mageplaza\SocialLogin\Helper\Data;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Customer\Model\Registration;
use Magento\Framework\Escaper;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Controller\AbstractAccount;
use Zigly\Login\Model\OtpreportFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;

/**
 * Class Create
 *
 * @package Zigly\Login\Controller\Popup
 */
class Create extends \Mageplaza\SocialLogin\Controller\Popup\Create
{
    /**
     * @var Validator
     */
    private $formKeyValidator;
    protected $inlineTranslation;
 
    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private $cookieMetadataManager;
 
    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @type JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @type CaptchaData
     */
    protected $captchaHelper;

    /**
     * @type Data
     */
    protected $socialHelper;

    /**
     * @param Context $context,
     * @param Session $customerSession,
     * @param ScopeConfigInterface $scopeConfig,
     * @param StoreManagerInterface $storeManager,
     * @param AccountManagementInterface $accountManagement,
     * @param Address $addressHelper,
     * @param UrlFactory $urlFactory,
     * @param FormFactory $formFactory,
     * @param SubscriberFactory $subscriberFactory,
     * @param RegionInterfaceFactory $regionDataFactory,
     * @param AddressInterfaceFactory $addressDataFactory,
     * @param CustomerInterfaceFactory $customerDataFactory,
     * @param CustomerUrl $customerUrl,
     * @param Registration $registration,
     * @param Escaper $escaper,
     * @param SerializerInterface $serializer,
     * @param OtpreportFactory $otpreportFactory,
     * @param CustomerExtractor $customerExtractor,
     * @param DataObjectHelper $dataObjectHelper,
     * @param AccountRedirect $accountRedirect,
     * @param CustomerRepository $customerRepository,
     * @param Validator $formKeyValidator
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $accountManagement,
        Address $addressHelper,
        UrlFactory $urlFactory,
        FormFactory $formFactory,
        SubscriberFactory $subscriberFactory,
        RegionInterfaceFactory $regionDataFactory,
        AddressInterfaceFactory $addressDataFactory,
        CustomerInterfaceFactory $customerDataFactory,
        CustomerUrl $customerUrl,
        Registration $registration,
        Escaper $escaper,
        SerializerInterface $serializer,
        OtpreportFactory $otpreportFactory,
        CustomerExtractor $customerExtractor,
        DataObjectHelper $dataObjectHelper,
        AccountRedirect $accountRedirect,
        CustomerRepository $customerRepository,
        Validator $formKeyValidator = null
    ) {
        $this->formKeyValidator = $formKeyValidator ?: ObjectManager::getInstance()->get(Validator::class);
        $this->accountRedirect = $accountRedirect;
        $this->otpreportFactory = $otpreportFactory;
        $this->serializer = $serializer;
        return parent::__construct($context,$customerSession,$scopeConfig,$storeManager,$accountManagement,$addressHelper,$urlFactory,$formFactory,$subscriberFactory,$regionDataFactory,$addressDataFactory,$customerDataFactory,$customerUrl,$registration,$escaper,$customerExtractor,$dataObjectHelper,$accountRedirect,$customerRepository,$formKeyValidator);
    }


    /**
     * @return JsonFactory|mixed
     */
    protected function getJsonFactory()
    {
        if (!$this->resultJsonFactory) {
            $this->resultJsonFactory = ObjectManager::getInstance()->get(JsonFactory::class);
        }

        return $this->resultJsonFactory;
    }

    /**
     * @return CaptchaData|mixed
     */
    protected function getCaptchaHelper()
    {
        if (!$this->captchaHelper) {
            $this->captchaHelper = ObjectManager::getInstance()->get(CaptchaData::class);
        }

        return $this->captchaHelper;
    }

    /**
     * @return Data|mixed
     */
    protected function getSocialHelper()
    {
        if (!$this->socialHelper) {
            $this->socialHelper = ObjectManager::getInstance()->get(Data::class);
        }

        return $this->socialHelper;
    }

    /**
     * Check default captcha
     *
     * @return bool
     */
    public function checkCaptcha()
    {
        $formId       = 'user_create';
        $captchaModel = $this->getCaptchaHelper()->getCaptcha($formId);
        $resolve      = $this->getSocialHelper()->captchaResolve($this->getRequest(), $formId);

        return !($captchaModel->isRequired() && !$captchaModel->isCorrect($resolve));
    }
 
    /**
     * Create customer account action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /**
         * @var Json $resultJson
         */
        $resultJson = $this->getJsonFactory()->create();
        $result     = [
            'success' => false,
            'message' => []
        ];
        if (!$this->checkCaptcha()) {
            $result['message'] = __('Incorrect CAPTCHA.');

            return $resultJson->setData($result);
        }

        if ($this->session->isLoggedIn() || !$this->registration->isAllowed()) {
            $result['redirect'] = $this->urlModel->getUrl('customer/account');

            return $resultJson->setData($result);
        }

        if (!$this->getRequest()->isPost()) {
            $result['message'] = __('Data error. Please try again.');

            return $resultJson->setData($result);
        }

        $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/cc-'.$now->format('d-m-Y').'.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r("------------START-------------", true));
        if (!empty($this->_request->getParam('email')) && !empty($this->_request->getParam('phone_number'))) {
            $logger->info(print_r("first if in", true));
            $now = new \DateTime();
            $now->add(new \DateInterval('PT10M'));
            $otpreport = $this->otpreportFactory->create()->getCollection()
                            ->addFieldToFilter('username',array(
                                array('eq' => '91'.$this->_request->getParam('phone_number')),
                                array('eq' => $this->_request->getParam('email')),
                            ))
                            ->addFieldToFilter('flag', array('eq' => 1))
                            ->addFieldToFilter('updated_at', ['lteq' => $now->format('Y-m-d H:i:s')])
                            ->setOrder('entity_id','DESC')
                            ->setPageSize('1');
            $logger->info(print_r($otpreport->getSelect()->__toString(), true));
            if(count($otpreport) < 0) {
                $logger->info(print_r("second if in", true));
                throw new CouldNotSaveException(__('We can\'t save the customer'));
            }
        }
        $logger->info(print_r("-------------END--------------", true));

        $this->session->regenerateId();

        try {
            $address   = $this->extractAddress();
            $addresses = $address === null ? [] : [$address];

            $customer = $this->customerExtractor->extract('customer_account_create', $this->_request);
            $customer->setAddresses($addresses);

            $password     = $this->getRequest()->getParam('password');
            $confirmation = $this->getRequest()->getParam('password_confirmation');

            if (!$this->checkPasswordConfirmation($password, $confirmation)) {
                $result['message'][] = __('Please make sure your passwords match.');
            } else {
                $customer = $this->accountManagement
                    ->createAccount($customer, $password);

                if ($this->getRequest()->getParam('is_subscribed', false)) {
                    $this->subscriberFactory->create()->subscribeCustomerById($customer->getId());
                }

                $this->_eventManager->dispatch(
                    'customer_register_success',
                    [
                        'account_controller' => $this,
                        'customer'           => $customer
                    ]
                );

                $confirmationStatus = $this->accountManagement->getConfirmationStatus($customer->getId());

                if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
                    $email = $this->customerUrl->getEmailConfirmationUrl($customer->getEmail());
                    // @codingStandardsIgnoreStart
                    $result['success'] = true;
                    $this->messageManager->addSuccess(
                        __(
                            'You must confirm your account. Please check your email for the confirmation link or <a href="%1">click here</a> for a new link.',
                            $email
                        )
                    );
                } else {
                    $result['success']   = true;
                    $result['message'][] = __('Create an account successfully. Please wait...');
                    $this->session->setCustomerDataAsLoggedIn($customer);
                }

                if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                    $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                    $metadata->setPath('/');
                    $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
                }
            }
        } catch (StateException $e) {
            $url = $this->urlModel->getUrl('customer/account/forgotpassword');
            // @codingStandardsIgnoreStart
            $result['message'][] = __(
                'There is already an account with this email address. If you are sure that it is your email address, <a href="%1">click here</a> to get your password and access your account.',
                $url
            );
        } catch (InputException $e) {
            $result['message'][] = $this->escaper->escapeHtml($e->getMessage());
            foreach ($e->getErrors() as $error) {
                $result['message'][] = $this->escaper->escapeHtml($error->getMessage());
            }
        } catch (LocalizedException $e) {
            $result['message'][] = $this->escaper->escapeHtml($e->getMessage());
        } catch (Exception $e) {
            $result['message'][] = __('We can\'t save the customer.');
        }

        $result['url'] = $this->_loginPostRedirect();
        $this->session->setCustomerFormData($this->getRequest()->getPostValue());

        return $resultJson->setData($result);
    }

    /**
     * Retrieve cookie manager
     *
     * @return PhpCookieManager
     * @deprecated
     */
    protected function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = ObjectManager::getInstance()->get(
                PhpCookieManager::class
            );
        }

        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @return CookieMetadataFactory
     * @deprecated
     */
    protected function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = ObjectManager::getInstance()->get(
                CookieMetadataFactory::class
            );
        }

        return $this->cookieMetadataFactory;
    }

    /**
     * Make sure that password and password confirmation matched
     *
     * @param string $password
     * @param string $confirmation
     *
     * @return boolean
     */
    protected function checkPasswordConfirmation($password, $confirmation)
    {
        return $password === $confirmation;
    }

    /**
     * Return redirect url by config
     *
     * @return mixed
     */
    protected function _loginPostRedirect()
    {
        $url = $this->_url->getUrl('customer/account');

        $object = ObjectManager::getInstance()->create(DataObject::class, ['url' => $url]);
        $this->_eventManager->dispatch('social_manager_get_login_redirect', [
            'object'  => $object,
            'request' => $this->_request
        ]);
        $url = $object->getUrl();

        return $url;
    }
}