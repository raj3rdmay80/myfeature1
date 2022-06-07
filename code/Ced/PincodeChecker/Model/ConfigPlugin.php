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
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\PincodeChecker\Model;

/**
 * Class ConfigPlugin
 * @package Ced\PincodeChecker\Model
 */
class ConfigPlugin
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
     * @var PincodeFactory
     */
    protected $pincodeFactory;

    /**
     * ConfigPlugin constructor.
     * @param \Ced\PincodeChecker\Helper\Data $helper
     * @param \Magento\Checkout\Model\Cart $checkoutSession
     * @param PincodeFactory $pincodeFactory
     */
    public function __construct(
        \Ced\PincodeChecker\Helper\Data $helper,
        \Magento\Checkout\Model\Cart $checkoutSession,
        \Ced\PincodeChecker\Model\PincodeFactory $pincodeFactory
    )
    {
        $this->_helper = $helper;
        $this->_checkoutSession = $checkoutSession;
        $this->pincodeFactory = $pincodeFactory;
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Magento\Payment\Model\MethodInterface[]
     * @api
     */
    public function aftercreate($subject, $result)
    {
        if (!$this->_helper->isPinCodeCheckerEnabled())
            return $result;
        $quote = $this->_checkoutSession->getQuote();
        if ($quote && $quote->getShippingAddress()->getPostcode()) {
            $pincode_model = $this->pincodeFactory->create();
            /*if no values exist in DB then return the  previous result*/
            if (count($pincode_model->getCollection()->getData()) <= 0) {
                return $result;
            }
            $zipcode = $quote->getShippingAddress()->getPostcode();
            $pincode_model = $pincode_model->load($zipcode, 'zipcode')->getData();
            if (!empty($pincode_model)) {
                if ($pincode_model['can_ship']) {
                    return $result;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return $result;
        }
    }
}
