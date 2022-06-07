<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Zigly\Managepets\Model\ManagepetsFactory;
use \Magento\Customer\Model\SessionFactory;

class Delete extends \Magento\Framework\App\Action\Action
{
    protected $connection;
    protected $resource;
    protected $resultRedirect;
    protected $messageManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        SessionFactory $customerSession,
        ManagepetsFactory $managepetsFactory,
        ResultFactory $resultRedirect
    )
    {
        $this->resultRedirect=$resultRedirect;
        $this->messageManager = $messageManager;
        $this->customerSession = $customerSession;
        $this->managepetsFactory = $managepetsFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        $id=$this->getRequest()->getParam('id');
        $customerId = $this->customerSession->create()->getCustomer()->getId();
        if ($customerId) {
            try{
                if((int)$id){
                    $pet = $this->managepetsFactory->create()->load((int)$id);
                    if ($pet->getCustomerId() == $customerId) {
                        $pet->delete();
                        $this->messageManager->addSuccess( __('Pet deleted Successfully !') );
                    } else {
                        $this->messageManager->addError("Something went wrong.");
                    }
                }else{
                    $this->messageManager->addError("Delete id wrongly passed".$id);
                }

            }catch(Exception $e){
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            $this->messageManager->addError("Something went wrong.");
        }

        return $resultRedirect;
    }


}
