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

namespace Ced\PincodeChecker\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class Check
 * @package Ced\PincodeChecker\Block
 */
class Check extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * Check constructor.
     * @param \Magento\Framework\Registry $registry
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Framework\Registry $registry, Template\Context $context, array $data = [])
    {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\Phrase|mixed
     */
    public function getZipCodeLabel()
    {
        return $this->_scopeConfig->getValue('pincode/general/pincode_label', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) ? $this->_scopeConfig->getValue('pincode/general/pincode_label', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) : __('Pincode : ');
    }
}
