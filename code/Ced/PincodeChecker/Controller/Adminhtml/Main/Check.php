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
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\PincodeChecker\Controller\Main;

use Magento\Framework\App\Action\Context;

/**
 * Class Check
 * @package Ced\PincodeChecker\Controller\Main
 */
class Check extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Ced\PincodeChecker\Helper\Feed
     */
    protected $feedHelper;

    /**
     * Check constructor.
     * @param \Ced\PincodeChecker\Helper\Feed $feedHelper
     * @param Context $context
     */
    public function __construct(\Ced\PincodeChecker\Helper\Feed $feedHelper, Context $context)
    {
        $this->feedHelper = $feedHelper;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $json = array('success' => 0, 'module_name' => '', 'module_license' => '');
        if ($data && isset($data['module_name'])) {
            $json['module_name'] = strtolower($data['module_name']);
            $json['module_license'] = $this->feedHelper->getStoreConfig(\Ced\PincodeChecker\Block\Extensions::HASH_PATH_PREFIX . strtolower($data['module_name']));
            if (strlen($json['module_license']) > 0) $json['success'] = 1;
            $this->getResponse()->setHeader('Content-type', 'application/json');
            echo json_encode($json);
            die;
        } else {
            $this->_forward('noroute');
        }
    }
}