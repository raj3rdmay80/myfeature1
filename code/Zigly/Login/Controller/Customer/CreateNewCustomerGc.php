<?php
namespace Zigly\Login\Controller\Customer;

use Magento\Quote\Model\QuoteFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Area;
use Psr\Log\LoggerInterface;
use Zigly\Login\Helper\Notification;


class CreateNewCustomerGc extends Action
{
    const  ZIGLY_LOGIN_CREATE_NEW_CUSTOMER_GUEST_CUSTOMER ='zigly_login_create_new_customer_guest_customer';

    public function __construct(
        Context $context,
        QuoteFactory $quoteFactory,
        StoreManagerInterface $storeManager,
        AddressInterfaceFactory $dataAddressFactory,
        AddressRepositoryInterface $addressRepository,
        CustomerRepositoryInterface $customerRepository,
        CustomerCollectionFactory $customerCollectionFactory,
        CustomerFactory $customerFactory,
        SessionFactory $sessionFactory,
        CheckoutSession $checkoutSession,
        RedirectFactory $resultRedirectFactory,
        JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        Notification $notification

    ) {
        $this->quoteFactory = $quoteFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->storeManager       = $storeManager;
        $this->dataAddressFactory = $dataAddressFactory;
        $this->addressRepository  = $addressRepository;
        $this->customerRepository = $customerRepository;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->sessionFactory = $sessionFactory;
        $this->checkoutSession = $checkoutSession;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->notification = $notification;
        parent::__construct($context);
    }

    public function execute()
    {
        $templatevariable['status'] = 0;
        $templatevariable['msg'] = "something went wrong";
        $data = $this->getRequest()->getPost();

        $quoteId = (int)$this->checkoutSession->getQuote()->getId();
        if($quoteId){
            $quote = $this->quoteFactory->create()->load($quoteId);
            if($quote->getData('gc_phone_number'))
            {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer_is_guest.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info('*********************************************************************'); 
                $logger->info('*******************************STARTED*******************************'); 


                $customerCollection = $this->customerCollectionFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('phone_number',$quote->getData('gc_phone_number'));
                $logger->info(print_r($customerCollection->getData(), true));
                if(!$customerCollection->count())
                {
                    $logger->info('*******************************quote*******************************');                     
                    $logger->info(print_r($quote->getData(), true));
                    $quoteAddress = $quote->getBillingAddress();

                    $logger->info('*******************************getBillingAddress*******************************');                     
                    $logger->info(print_r($quoteAddress->getData(), true));
                    $firstName = $quoteAddress->getData('firstname');
                    $lastName = ($quoteAddress->getData('lastname'))? $quoteAddress->getData('lastname') : '';
                    $customerName = ($lastName)? $firstName.' '.$lastName:$firstName;
                    $street = $quoteAddress->getStreet()[0];
                    $street1 = (isset($quoteAddress->getStreet()[1]))? $quoteAddress->getStreet()[1] :"";
                    $countryId = $quoteAddress->getData('country_id');
                    $regionId = $quoteAddress->getData('region_id');
                    $postcode = $quoteAddress->getData('postcode');
                    $city = $quoteAddress->getData('city');
                    $telephone = $quoteAddress->getData('telephone');
                    $signInPhoneNumber = $quote->getData('gc_phone_number');
                    $email = ($data['email'])? $data['email']: $quoteAddress->getData('email');
                    $logger->info('*******************************STARTED __getBillingAddress*******************************');                     
                    $logger->info($firstName);                     
                    $logger->info($lastName);                     
                    $logger->info($street);                     
                    $logger->info($countryId);                     
                    $logger->info($regionId);                     
                    $logger->info($postcode);                     
                    $logger->info($city);                     
                    $logger->info($telephone);                     
                    $logger->info($signInPhoneNumber);                     
                    $logger->info($email);                     
                    $logger->info('*******************************END __getBillingAddress*******************************');                     
                    try
                    {
                        /*create a new customer*/
                        $store     = $this->storeManager->getStore();
                        $websiteId = $this->storeManager->getStore()->getWebsiteId();
                        $customer = $this->customerFactory->create();
                        $customer->setWebsiteId($websiteId)
                                ->setStore($store)
                                ->setEmail($email)
                                ->setFirstname($firstName);
                        if($lastName){
                            $customer->setLastname($lastName);
                        }
                        $customer->save();
                        $logger->info('*******************************customer*******************************');                     
                        $logger->info(print_r($customer->getData(), true));

                        $logger->info('*******************************sign*******************************');                     
                        $customer = $this->customerRepository->getById($customer->getId());
                        $customer->setCustomAttribute('phone_number',$signInPhoneNumber);
                        $customer->setCustomAttribute('is_approved','approved');
                        $customerData = $this->customerRepository->save($customer);
                        $logger->info('*******************************customer phone phone_number*******************************');                     

                        /*create a new address for created customer*/                    
                        $address = $this->dataAddressFactory->create();
                        $address->setFirstname($firstName);
                        $address->setTelephone($telephone);

                        $streets[] = $street;//pass street as array
                        if($street1){
                            $streets[] = $street1;
                        }
                        $address->setStreet($streets);

                        $address->setCity($city);
                        $address->setCountryId($countryId);
                        $address->setPostcode($postcode);
                        $address->setRegionId($regionId);
                        $address->setIsDefaultShipping(1);
                        $address->setIsDefaultBilling(1);
                        $address->setCustomerId($customer->getId());
                        
                        $addressData = $this->addressRepository->save($address);

                        $loginCustomer = $this->customerFactory->create()->load($customer->getId());
                        $sessionManager = $this->sessionFactory->create();
                        $sessionManager->setCustomerAsLoggedIn($loginCustomer);
                        
                        $template['template_id'] = self::ZIGLY_LOGIN_CREATE_NEW_CUSTOMER_GUEST_CUSTOMER;
                        $template['email'] = $email;
                        $template['data']['customername'] = $customerName;
                        $template['data']['store'] = $this->storeManager->getStore();

                        $this->notification->sendMail($template);

                        $templatevariable['status'] = 1;
                        $templatevariable['msg'] = 'success';



                    } catch (\Exception $e) {
                        $templatevariable['status'] = 0;
                        $templatevariable['msg'] = $e->getMessage();
                        $logger->info('error log');
                        $this->logger->critical($e->getMessage());
                        $this->inlineTranslation->resume();
                        $logger->info($e->getMessage()); 

                    }

                    // $resultRedirect = $this->resultRedirectFactory->create();
                    // $result = $resultRedirect->setPath('checkout/cart');
                    // return $result;
                }
            }
        }
        $result = $this->_resultJsonFactory->create();
        $resultPage = $this->_resultPageFactory->create();

        $result->setData($templatevariable);
        return $result;

    }

    /*
     * Get Current store id
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /*
     * Get Current store name
     */
    public function getStoreName()
    {
        return $this->storeManager->getStore()->getName();
    }

    /*
     * Get Current store Info
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }
    
    public function getConfigValue($template)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue($template, $storeScope);
    }   

}