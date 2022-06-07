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

namespace Ced\PincodeChecker\Helper;

use \Magento\OfflinePayments\Model\Cashondelivery;

/**
 * Class Data
 * @package Ced\PincodeChecker\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $_context;

    /**
     * @var \Magento\Payment\Model\Config
     */
    protected $_paymentConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ValueInterface
     */
    protected $_configValueManager;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ValueInterface $_configValueManager
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ValueInterface $_configValueManager,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_context = $context;
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $storeManager;
        $this->_configValueManager = $_configValueManager;
        $this->_transaction = $transaction;
        $this->_paymentConfig = $paymentConfig;
        $this->registry = $registry;
    }

    /**
     * function isEnable
     *
     * for checking if group gift module is enabled or not
     * @return Boolean
     */
    public function isPinCodeCheckerEnabled()
    {
        return $this->_scopeConfig->getValue('pincode/general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function getIsCodAllowedFromAdmin()
    {
        return in_array(
            Cashondelivery::PAYMENT_METHOD_CASHONDELIVERY_CODE,
            array_keys($this->_paymentConfig->getActiveMethods())
        );

    }

    /**
     * @return mixed
     */
    public function getCodAllowedText()
    {
        return $this->_scopeConfig->getValue(
            'pincode/general/cod_allowed_text',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getCodNotAllowedText()
    {
        return $this->_scopeConfig->getValue('pincode/general/cod_not_allowed_text', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getZipcodeNotFoundMessage()
    {
        return $this->_scopeConfig->getValue('pincode/general/zip_not_found', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $module
     * @return bool|string
     */
    public function getReleaseVersion($module)
    {
        $modulePath = $this->moduleRegistry->getPath(self::XML_PATH_INSTALLATED_MODULES, $module);
        $filePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, "$modulePath/etc/module.xml");
        $source = new \Magento\Framework\Simplexml\Config($filePath);
        if ($source->getNode(self::XML_PATH_INSTALLATED_MODULES)->attributes()->release_version) {
            return $source->getNode(self::XML_PATH_INSTALLATED_MODULES)->attributes()->release_version->__toString();
        }
        return false;
    }


    /**
     * Url encode the parameters
     *
     * @param string | array
     * @return string | array | boolean
     */
    public function prepareParams($data)
    {
        if (!is_array($data) && strlen($data)) {
            return urlencode($data);
        }
        if ($data && is_array($data) && count($data) > 0) {
            foreach ($data as $key => $value) {
                $data[$key] = urlencode($value);
            }
            return $data;
        }
        return false;
    }

    /**
     * Url decode the parameters
     *
     * @param string | array
     * @return string | array | boolean
     */
    public function extractParams($data)
    {
        if (!is_array($data) && strlen($data)) {
            return urldecode($data);
        }
        if ($data && is_array($data) && count($data) > 0) {
            foreach ($data as $key => $value) {
                $data[$key] = urldecode($value);
            }
            return $data;
        }
        return false;
    }

    /**
     * Add params into url string
     *
     * @param string $url (default '')
     * @param array $params (default array())
     * @param boolean $urlencode (default true)
     * @return string | array
     */
    public function addParams($url = '', $params = array(), $urlencode = true)
    {
        if (count($params) > 0) {
            foreach ($params as $key => $value) {
                if (parse_url($url, PHP_URL_QUERY)) {
                    if ($urlencode) {
                        $url .= '&' . $key . '=' . $this->prepareParams($value);
                    } else {
                        $url .= '&' . $key . '=' . $value;
                    }
                } else {
                    if ($urlencode) {
                        $url .= '?' . $key . '=' . $this->prepareParams($value);
                    } else {
                        $url .= '?' . $key . '=' . $value;
                    }
                }
            }
        }
        return $url;
    }

    /**
     * Retrieve all the extensions name and version developed by CedCommerce
     *
     * @param boolean $asString (default false)
     * @return array|string
     */
    public function getCedCommerceExtensions($asString = false)
    {
        if ($asString) {
            $cedCommerceModules = '';
        } else {
            $cedCommerceModules = array();
        }
        $allModules = $this->_context->getScopeConfig()->getValue(\Ced\PincodeChecker\Model\Feed::XML_PATH_INSTALLATED_MODULES);
        $allModules = json_decode(json_encode($allModules), true);
        foreach ($allModules as $name => $module) {
            $name = trim($name);
            if (preg_match('/ced_/i', $name) && isset($module['release_version'])) {
                if ($asString) {
                    $cedCommerceModules .= $name . ':' . trim($module['release_version']) . '~';
                } else {
                    $cedCommerceModules[$name] = trim($module['release_version']);
                }
            }
        }
        if ($asString) {
            trim($cedCommerceModules, '~');
        }
        return $cedCommerceModules;
    }

    /**
     * Retrieve environment information of magento
     * And installed extensions provided by CedCommerce
     *
     * @return array
     */
    public function getEnvironmentInformation()
    {
        $info = array();
        $info['domain_name'] = $this->_productMetadata->getBaseUrl();
        $info['magento_edition'] = 'default';
        if (method_exists('Mage', 'getEdition')) {
            $info['magento_edition'] = $this->_productMetadata->getEdition();
        }
        $info['magento_version'] = $this->_productMetadata->getVersion();
        $info['php_version'] = phpversion();
        $info['feed_types'] = $this->getStoreConfig(\Ced\PincodeChecker\Model\Feed::XML_FEED_TYPES);
        $info['installed_extensions_by_cedcommerce'] = $this->getCedCommerceExtensions(true);

        return $info;
    }

    /**
     * Retrieve admin interest in current feed type
     *
     * @param SimpleXMLElement $item
     * @return boolean $isAllowed
     */
    public function isAllowedFeedType(SimpleXMLElement $item)
    {
        $isAllowed = false;
        if (is_array($this->_allowedFeedType) && count($this->_allowedFeedType) > 0) {
            $cedModules = $this->getCedCommerceExtensions();
            switch (trim((string)$item->update_type)) {
                case \Ced\PincodeChecker\Model\Source\Updates\Type::TYPE_NEW_RELEASE :
                case \Ced\PincodeChecker\Model\Source\Updates\Type::TYPE_INSTALLED_UPDATE :
                    if (in_array(\Ced\PincodeChecker\Model\Source\Updates\Type::TYPE_INSTALLED_UPDATE, $this->_allowedFeedType) && strlen(trim($item->module)) > 0 && isset($cedModules[trim($item->module)]) && version_compare($cedModules[trim($item->module)], trim($item->release_version), '<') === true) {
                        $isAllowed = true;
                        break;
                    }
                case \Ced\PincodeChecker\Model\Source\Updates\Type::TYPE_UPDATE_RELEASE :
                    if (in_array(\Ced\PincodeChecker\Model\Source\Updates\Type::TYPE_UPDATE_RELEASE, $this->_allowedFeedType) && strlen(trim($item->module)) > 0) {
                        $isAllowed = true;
                        break;
                    }
                    if (in_array(\Ced\PincodeChecker\Model\Source\Updates\Type::TYPE_NEW_RELEASE, $this->_allowedFeedType)) {
                        $isAllowed = true;
                    }
                    break;
                case \Ced\PincodeChecker\Model\Source\Updates\Type::TYPE_PROMO :
                    if (in_array(\Ced\PincodeChecker\Model\Source\Updates\Type::TYPE_PROMO, $this->_allowedFeedType)) {
                        $isAllowed = true;
                    }
                    break;
                case \Ced\PincodeChecker\Model\Source\Updates\Type::TYPE_INFO :
                    if (in_array(\Ced\PincodeChecker\Model\Source\Updates\Type::TYPE_INFO, $this->_allowedFeedType)) {
                        $isAllowed = true;
                    }
                    break;
            }
        }
        return $isAllowed;
    }


    /**
     * Function for setting Config value of current store
     *
     * @param string $path ,
     * @param string $value ,
     */
    public function setStoreConfig($path, $value, $storeId = null)
    {
        $store = $this->_storeManager->getStore($storeId);
        $data = [
            'path' => $path,
            'scope' => 'stores',
            'scope_id' => $storeId,
            'scope_code' => $store->getCode(),
            'value' => $value,
        ];
        $this->_configValueManager->addData($data);
        $this->_transaction->addObject($this->_configValueManager);
        $this->_transaction->save();
    }

    /**
     * Function for getting Config value of current store
     *
     * @param string $path ,
     */
    public function getStoreConfig($path, $storeId = null)
    {

        $store = $this->_storeManager->getStore($storeId);
        return $this->_scopeConfig->getValue($path, 'store', $store->getCode());
    }

    /**
     * @return bool
     */
    public function getTypeAllowed()
    {
        $product = $this->registry->registry('current_product');
        if ($product->getTypeId() == 'downloadable' || $product->getTypeId() == 'virtual')
            return false;
        return true;
    }

    /**
     * @return mixed
     */
    public function showOnPage()
    {
        return $this->_scopeConfig->getValue('pincode/general/allowed_product_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
