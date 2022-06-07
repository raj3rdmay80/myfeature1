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

use Magento\Backend\App\Action\Context;

/**
 * Class Delete
 * @package Ced\PincodeChecker\Controller\Adminhtml\Pincodechecker
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\PincodeChecker\Model\PincodeFactory
     */
    protected $pincodeFactory;

    /**
     * Delete constructor.
     * @param \Ced\PincodeChecker\Model\PincodeFactory $pincodeFactory
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Ced\PincodeChecker\Model\PincodeFactory $pincodeFactory,
        Context $context,
        array $data = []
    )
    {
        $this->pincodeFactory = $pincodeFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($this->getRequest()->getParam("id") > 0) {
            try {
                $model = $this->pincodeFactory->create();
                $model->setId($this->getRequest()->getParam("id"))->delete();
                $this->messageManager->addSuccessMessage(__('Item is successfully deleted.'));
                $this->_redirect("*/*/");
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            }
        }
        $this->_redirect("*/*/");
    }
}
