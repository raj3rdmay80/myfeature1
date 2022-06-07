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

namespace Ced\PincodeChecker\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Validate
 * @package Ced\PincodeChecker\Controller\Index
 */
class Validate extends Action
{
    /**
     * @var \Ced\PincodeChecker\Helper\Data
     */
    protected $_helper;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Ced\PincodeChecker\Model\ResourceModel\Pincode\CollectionFactory
     */
    protected $pincodeCollectionFactory;

    /**
     * Validate constructor.
     * @param Context $context
     * @param \Ced\PincodeChecker\Helper\Data $helper
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultjson
     * @param \Ced\PincodeChecker\Model\ResourceModel\Pincode\CollectionFactory $pincodeCollectionFactory
     */
    public function __construct(
        Context $context,
        \Ced\PincodeChecker\Helper\Data $helper,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultjson,
        \Ced\PincodeChecker\Model\ResourceModel\Pincode\CollectionFactory $pincodeCollectionFactory
    )
    {
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultjson;
        $this->pincodeCollectionFactory = $pincodeCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return bool|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->_helper->isPinCodeCheckerEnabled())
            return false;
        $post_data = $this->getRequest()->getPost();
        $zip = $post_data['zip'];
        $can_cod_flag = false;
        $can_ship_flag = false;
        $days_to_deliver = '-';
        $zip_not_found = true;
        $pincode_model = $this->pincodeCollectionFactory->create()
            ->addFieldToFilter('vendor_id', 'admin')
            ->addFieldToFilter('zipcode', array('eq' => $zip))
            ->getData();
        if (count($pincode_model)) {
            foreach ($pincode_model as $model) {
                if ($model['can_cod'] == '1')
                    $can_cod_flag = true;
                if ($model['can_ship'] == '1')
                    $can_ship_flag = true;

                $days_to_deliver = $model['days_to_deliver'];
            }
        }
        $msg = $this->getDeliveryDaysMessage((int)$days_to_deliver, $zip);
        $response = array('cod' => $can_cod_flag,
            'ship' => $can_ship_flag,
            'days' => $msg,
            'zip_not_found' => $zip_not_found
        );

        $this->getResponse()->setBody($response);
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);

    }

    /**
     * @param $days
     * @param $pincode
     * @return mixed
     */
    protected function getDeliveryDaysMessage($days, $pincode)
    {
        $codes = array('{{from-days}}', '{{to-days}}', '{{pincode}}');
        $msg = $this->_scopeConfig->getValue('pincode/general/delivery_text', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $margin_days = (int)$this->_scopeConfig->getValue('pincode/general/delivery_days_margin', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $replace_string = array($days, $days + $margin_days, $pincode);
        return str_replace($codes, $replace_string, $msg);
    }
}
