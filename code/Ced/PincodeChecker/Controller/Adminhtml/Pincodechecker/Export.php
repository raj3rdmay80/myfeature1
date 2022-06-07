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
use Magento\Framework\App\Filesystem\DirectoryList;

class Export extends \Magento\Backend\App\Action
{
    
    protected $_coreRegistry = null;

    protected $_fileFactory;

    protected $resultPageFactory;
   
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

	public function execute()
    {
        $fileName = 'pincode_checker.csv';
        $gridBlock = $this->_view->getLayout()->createBlock( 
            'Ced\PincodeChecker\Block\Adminhtml\Pincode\Grid'
        );
        $content = $gridBlock->getCsvFile();
        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
