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

namespace Ced\PincodeChecker\Block\Adminhtml\Extensions;

/**
 * Class Details
 * @package Ced\PincodeChecker\Block\Adminhtml\Extensions
 */
class Details extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var \Ced\PincodeChecker\Helper\Feed
     */
    protected $feedHelper;

    /**
     * @var \Ced\PincodeChecker\Helper\Data
     */
    protected $dataHelper;

    /**
     * Details constructor.
     * @param \Ced\PincodeChecker\Helper\Feed $feedHelper
     * @param \Ced\PincodeChecker\Helper\Data $dataHelper
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Ced\PincodeChecker\Helper\Feed $feedHelper,
        \Ced\PincodeChecker\Helper\Data $dataHelper,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    )
    {
        $this->feedHelper = $feedHelper;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return $this|string
     */
    public function getModules()
    {
        $modules = $this->feedHelper->getCedCommerceExtensions();
        $helper = $this->dataHelper;
        $params = array();
        $args = '';
        foreach ($modules as $moduleName => $releaseVersion) {
            $m = strtolower($moduleName);
            if (!preg_match('/ced/i', $m)) {
                return $this;
            }
            $h = $helper->getStoreConfig(\Ced\PincodeChecker\Block\Extensions::HASH_PATH_PREFIX . $m . '_hash');
            for ($i = 1; $i <= (int)$helper->getStoreConfig(\Ced\PincodeChecker\Block\Extensions::HASH_PATH_PREFIX . $m . '_level'); $i++) {
                $h = base64_decode($h);
            }
            $h = json_decode($h, true);
            if (is_array($h) && isset($h['domain']) && isset($h['module_name']) && isset($h['license']) && strtolower($h['module_name']) == $m && $h['license'] == $helper->getStoreConfig(\Ced\PincodeChecker\Block\Extensions::HASH_PATH_PREFIX . $m)) {
            } else {
                $args .= $m . ',';
            }
        }

        $args = trim($args, ',');
        return $args;

    }

}