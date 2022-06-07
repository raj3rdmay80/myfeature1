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

namespace Ced\PincodeChecker\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Validate
 * @package Ced\PincodeChecker\Observer
 */
Class Validate implements ObserverInterface
{
    /**
     * @var \Ced\PincodeChecker\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_checkoutSession;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Ced\PincodeChecker\Model\ResourceModel\Pincode\CollectionFactory
     */
    protected $pincodeFactory;

    /**
     * Validate constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param \Ced\PincodeChecker\Helper\Data $helper
     * @param \Magento\Checkout\Model\Cart $checkoutSession
     * @param \Ced\PincodeChecker\Model\ResourceModel\Pincode\CollectionFactory $pincodeFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Ced\PincodeChecker\Helper\Data $helper,
        \Magento\Checkout\Model\Cart $checkoutSession,
        \Ced\PincodeChecker\Model\ResourceModel\Pincode\CollectionFactory $pincodeFactory
    )
    {
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
        $this->_checkoutSession = $checkoutSession;
        $this->pincodeFactory = $pincodeFactory;
    }

    /**
     *redirect on advance order link
     *
     * @param $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if (!$this->_helper->isPinCodeCheckerEnabled())
            return false;
        $event = $observer->getEvent();
        $method = $event->getMethodInstance();
        $result = $event->getResult();
        $quote = $this->_checkoutSession->getQuote();
        if ($quote && $result->getIsAvailable() && $quote->getShippingAddress()->getPostcode()) {
            $zipcode = $quote->getShippingAddress()->getPostcode();
            $pincode_model = $this->pincodeFactory->create()
                ->addFieldToFilter('vendor_id', 'admin')
                ->addFieldToFilter('zipcode', array('eq' => $zipcode))
                ->getData();

            $methods_to_restrict = explode(",", $this->_scopeConfig->getValue('pincode_general_methods_to_restrict', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            if (count($pincode_model)) {
                foreach ($pincode_model as $model) {
                    if ($model['can_ship']) {
                        if ($method->getCode() == 'cashondelivery' && $model ['can_cod'] == '1') {
                            $result->setData('is_available', true);
                            return;
                            break;
                        } elseif ($method->getCode() == 'cashondelivery' && $model ['can_cod'] == '0') {
                            $result->setData('is_available', false);
                            return;
                            break;
                        } else {
                            $result->setData('is_available', true);
                            return;
                            break;
                        }
                    } else {
                        $result->setData('is_available', false);
                        return;
                        break;
                    }
                }
            } else {
                $result->setData('is_available', false);
                return;
            }
        }

    }
}
