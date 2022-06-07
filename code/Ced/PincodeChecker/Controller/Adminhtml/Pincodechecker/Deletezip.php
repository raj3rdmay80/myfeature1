<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_PincodeChecker
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\PincodeChecker\Controller\Adminhtml\Pincodechecker;

use Magento\Backend\App\Action;

/**
 * Class Deletezip
 * @package Ced\PincodeChecker\Controller\Adminhtml\Pincodechecker
 */
class Deletezip extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\PincodeChecker\Model\PincodeFactory
     */
    protected $pincodeFactory;

    /**
     * Deletezip constructor.
     * @param \Ced\PincodeChecker\Model\PincodeFactory $pincodeFactory
     * @param Action\Context $context
     */
    public function __construct(\Ced\PincodeChecker\Model\PincodeFactory $pincodeFactory, Action\Context $context)
    {
        $this->pincodeFactory = $pincodeFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('id');
        if (!is_array($ids)) {
            $this->messageManager->addErrorMessage(__('Please select item(s).'));
        } else {
            if (!empty($ids)) {
                try {
                    foreach ($ids as $id) {
                        $this->pincodeFactory->create()->load($id)->delete();
                    }
                    $this->messageManager->addSuccessMessage(__('Total of %1 record(s) have been deleted.', count($ids)));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }
}
