<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
namespace Zigly\VideoIntegrate\Controller\Test;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

class Test extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
		\Magento\Framework\View\LayoutInterface $layout

    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->_layout = $layout;
        parent::__construct($context);
    }
	
	/**
	* @return json object
	*/
    public function execute()
    {
		return $this->resultPageFactory->create();
    }
}
