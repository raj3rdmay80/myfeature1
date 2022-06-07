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
 * Class Save
 * @package Ced\PincodeChecker\Controller\Adminhtml\Pincodechecker
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\PincodeChecker\Model\PincodeFactory
     */
    protected $pincodeFactory;

    /**
     * Save constructor.
     * @param \Ced\PincodeChecker\Model\PincodeFactory $pincodeFactory
     * @param Action\Context $context
     */
    public function __construct(\Ced\PincodeChecker\Model\PincodeFactory $pincodeFactory, Action\Context $context)
    {
        $this->pincodeFactory = $pincodeFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = $this->pincodeFactory->create();
            $id = $this->getRequest()->getParam('id');
            $post_data = $this->getRequest()->getPostValue();
            $zipcode = $post_data['zipcode'];
            if ($id) {
                $model->load($id);
            } else {
                $existing_pincode = $model->getCollection()->addFieldToFilter('zipcode', $zipcode)->getData();
                if (!empty($existing_pincode)) {
                    $this->messageManager->addErrorMessage(__('Zipcode already exists.'));
                    $this->_redirect("*/*/");
                    return;
                }

            }
            $model->addData($post_data);
            $model->save();
            $this->messageManager->addSuccessMessage(__('Zip Code Saved Successfully.'));
        } else
            $this->messageManager->addErrorMessage(__('No data found to save.'));
        $this->_redirect("*/*/");

    }
}