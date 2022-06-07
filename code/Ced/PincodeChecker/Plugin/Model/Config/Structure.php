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
* @category Ced
* @package Ced_PincodeChecker
* @author CedCommerce Core Team <connect@cedcommerce.com>
* @copyright Copyright CedCommerce (https://cedcommerce.com/)
* @license https://cedcommerce.com/license-agreement.txt
*/

namespace Ced\PincodeChecker\Plugin\Model\Config;

/**
* Class Structure
* @package Ced\PincodeChecker\Plugin\Model\Config
*/
class Structure{

	/**
	* @var \Ced\CsMarketplace\Helper\Feed
	*/
	protected $feedHelper;

	/**
	* Structure constructor.
	* @param \Ced\CsMarketplace\Helper\Feed $feedHelper
	*/
	public function __construct(
	\Ced\PincodeChecker\Helper\Feed $feedHelper
	){
	  $this->feedHelper = $feedHelper;
	}

	/**
	* @param \Magento\Config\Model\Config\Structure $subject
	* @param $result
	* @return array
	* @throws \Magento\Framework\Exception\FileSystemException
	* @throws \Magento\Framework\Exception\LocalizedException
	*/
	public function afterGetFieldPaths(\Magento\Config\Model\Config\Structure $subject, $result)
	{
		$modules = $this->feedHelper->getAllModules();
		foreach ($modules as $moduleName => $children) {
			$path = 'cedcore/extensions/extension_' . strtolower($moduleName);
			$config_paths[$path] = [$path];
		}
		$result = array_merge($result, $config_paths);
		return $result;
	}
}
