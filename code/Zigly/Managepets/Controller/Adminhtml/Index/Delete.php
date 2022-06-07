<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Controller\Adminhtml\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Controller\RegistryConstants;

class Delete extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    protected $session;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Zigly\Managepets\Model\ManagepetsFactory $managepetsFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory
        )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->managepetsFactory = $managepetsFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
    }
    public function execute()
    {
            $result = $this->_resultJsonFactory->create();
            $resultPage = $this->resultPageFactory->create();
            $success = 0;
            $petid = $this->getRequest()->getParam('petid');
            $customerid = $this->getRequest()->getParam('customerid');
            $model = $this->managepetsFactory->create()->load($petid);
            try {
                $model->delete();
                $success = 1;
                $block = $resultPage->getLayout()
                ->createBlock('Zigly\Managepets\Block\Adminhtml\Edit\Tab\View\Managepets')->setManualCustomerId($customerid)->toHtml();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $block = $e->getMessage();
            } catch (\RuntimeException $e) {
                $block = $e->getMessage();
            } catch (\Exception $e) {
               $block = $e->getMessage();
            }
            $result->setData(['output' => $block,'success' => $success]);
            return $result;
        }
}
