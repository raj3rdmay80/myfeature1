<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_PincodeChecker
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */ 

namespace Ced\PincodeChecker\Controller\Adminhtml\Pincodechecker;

use Magento\Backend\App\Action;

class Import extends \Magento\Backend\App\Action
{
    
    protected $_coreRegistry = null;

   
    protected $resultPageFactory;

   
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    
    
    protected function _initAction()
    {
    	$resultPage = $this->resultPageFactory->create();
    	$resultPage->setActiveMenu('Ced_PincodeChecker::pincodechecker_pincodechecker')
    	->addBreadcrumb(__('PincodeChecker'), __('PincodeChecker'))
    	->addBreadcrumb(__('Import CSV'), __('Import CSV'));
    	return $resultPage;
    }
   
    protected function _isAllowed()
    {
        return true;
    }

	public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(__('Import CSV'),__('Import CSV'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import CSV'));
        return $resultPage;
    }
}
