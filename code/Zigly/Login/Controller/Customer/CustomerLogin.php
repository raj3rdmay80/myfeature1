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
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;


class CustomerLogin extends Action
{

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory,
        CustomerCollectionFactory $customerCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\SessionFactory $sessionFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->sessionFactory = $sessionFactory;
        parent::__construct($context);
    }


    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $username = $data['username'];
        $templatevariable['status'] = 0;

        if($username && preg_match("/^[1-9][0-9]{9}$/", $username)){
            $customerCollection = $this->customerCollectionFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('phone_number',$username);
             if($customerCollection->count()){
                $customerData = $customerCollection->getData(); 
                $customer = $this->customerFactory->create()->load($customerData[0]['entity_id']);
                $sessionManager = $this->sessionFactory->create();
                $sessionManager->setCustomerAsLoggedIn($customer);
                $templatevariable['msg'] = 'success';
                $templatevariable['status'] = 1;
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

