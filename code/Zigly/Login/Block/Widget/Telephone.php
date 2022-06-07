<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Block\Widget;

use Magento\Customer\Block\Widget\Telephone as Telephonenumber;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\Address as AddressHelper;
use Magento\Customer\Model\Options;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\AddressFactory;

/**
 * Widget for showing customer telephone.
 *
 * @method TelephoneInterface getObject()
 * @method Telephone setObject(TelephoneInterface $telephone)
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Telephone extends Telephonenumber
{
    /**
     * @var AddressFactory
     */
    protected $address;

    /**
     * @param Context $context
     * @param AddressHelper $addressHelper
     * @param CustomerMetadataInterface $customerMetadata
     * @param Options $options
     * @param AddressFactory $address,
     * @param AddressMetadataInterface $addressMetadata
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Customer\Model\Session $customerSession
     */
    
    public function __construct(
        Context $context,
        AddressHelper $addressHelper,
        CustomerMetadataInterface $customerMetadata,
        Options $options,
        AddressFactory $address,
        AddressMetadataInterface $addressMetadata,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\Session $customerSession,
        
        array $data = []
    ) {
        $this->options = $options;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($context, $addressHelper, $customerMetadata, $options, $addressMetadata, $data);
        $this->addressMetadata = $addressMetadata;
        $this->_isScopePrivate = true;
        $this->address = $address;
        $this->customerSession = $customerSession;
    }
    /**
     * @inheritdoc
     */
    /**
     * Construct
     *
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Zigly_Login::widget/telephone.phtml');
    }

    /**
     * Define if LastName attribute can be shown
     *
     * @return bool
     */
    public function showLastName()
    {
        return $this->_isAttributeVisible('telephone');
        return;
    }

    /**
     * Check if attribute is visible
     *
     * @param string $attributeCode
     * @return bool
     */
    private function _isAttributeVisible($attributeCode)
    {
        $attributeMetadata = $this->_getAttribute($attributeCode);
        return $attributeMetadata ? (bool)$attributeMetadata->isVisible() : false;
    }

    /**
     * get the phone number attribute
     *
     *
     * @return integer
     */
    public function getcustomerphonenumber()
    {
        $customerid = $this->customerSession->getCustomerId();
        $customer =$this->customerRepositoryInterface->getById($customerid);
        $phone_number = $customer->getCustomAttribute('phone_number');
        $phone_number = is_null($phone_number) ? "" : $phone_number->getvalue(); 
        return $phone_number;
    }

    public function getFirstName()
    {
        $customer = $this->customerSession->getCustomer();
        $shippingAddress = $this->address->create()->load($customer->getDefaultShipping());
        return $shippingAddress->getFirstname();
    }

    public function getLastName()
    {
        $customer = $this->customerSession->getCustomer();
        $shippingAddress = $this->address->create()->load($customer->getDefaultShipping());
        return $shippingAddress->getLastname();
    }
}
