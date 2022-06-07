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

namespace Ced\PincodeChecker\Model;

use Magento\Framework\App\Helper\Context;

/**
 * Class Import
 * @package Ced\PincodeChecker\Model
 */
class Import extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Payment\Model\Config
     */
    protected $paymentConfig;

    /**
     * Import constructor.
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param Context $context
     */
    public function __construct(\Magento\Payment\Model\Config $paymentConfig, Context $context)
    {
        $this->paymentConfig = $paymentConfig;
        parent::__construct($context);
    }

    /**
     * Retrieve Option values array
     *
     * @param boolean $defaultValues
     * @param boolean $withEmpty
     * @return array
     */
    public function toOptionArray($defaultValues = false, $withEmpty = false, $storeId = null)
    {
        return $this->getActivPaymentMethods();
    }

    /**
     * @return array
     */
    public function getActivPaymentMethods()
    {
        $payments = $this->paymentConfig->getActiveMethods();
        $methods = array();

        foreach ($payments as $paymentCode => $paymentModel) {

            $paymentTitle = $this->_scopeConfig->getValue('payment/' . $paymentCode . '/title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($paymentCode == 'cashondelivery')
                continue;
            $methods[] = array(
                'label' => $paymentTitle,
                'value' => $paymentCode,
            );
        }
        return $methods;
    }
}
