<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Controller\Customer;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;


class IsApprovedCustomer extends Action
{

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory,
        AccountManagementInterface $customerAccountManagement,
        StoreManagerInterface $storeManager,
        CustomerCollectionFactory $customerCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        SerializerInterface $serializer
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->storeManager = $storeManager;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->serializer = $serializer;
        parent::__construct($context);
    }


    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $username = $data['username'];
        $templatevariable['is_approved'] = 0;
        $templatevariable['existing_customer'] = 0;
        $templatevariable['msg'] = 'success';

        if($username && preg_match("/^[1-9][0-9]{9}$/", $username)){
            $customerCollection = $this->customerCollectionFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('phone_number',$username);
             if($customerCollection->count()){
                $customerData = $customerCollection->getData();    
                $templatevariable['existing_customer'] = 1;
                $customer = $this->customerRepositoryInterface->getById($customerData[0]['entity_id']);
                $IsApproved = ($customer->getCustomAttribute('is_approved')) ? $customer->getCustomAttribute('is_approved')->getValue(): '';
                if($IsApproved == 'approved'){
                    $templatevariable['is_approved'] = 1;
                }else{
                    $templatevariable['msg'] = 'Your account is temporarily disabled. Please try with different number.';
                }
            }
        }else{
            $templatevariable['msg'] = 'something went wrong';    
        }
        $result = $this->_resultJsonFactory->create();
        $resultPage = $this->_resultPageFactory->create();

        $result->setData($templatevariable);
        return $result;
    }
}

