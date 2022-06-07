<?php
/**
 * Copyright © 2017 CommerceExtensions. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CommerceExtensions\ProductImportExport\Controller\Adminhtml\Data;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Config\Model\Config\Backend\Image as SourceImage;

class Progress extends \CommerceExtensions\ProductImportExport\Controller\Adminhtml\Data
{	
	/**
     * import action from import/export data
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
	 
	protected function _getStoreConfig($path, $storeId)
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
	protected function _getNotCachedRow( $path, $storeId)
    {
        $type = 'product';
        $cfg = $this->_getStoreConfig($path . '/' . $type, $storeId);

        $scope   = 'default';
        $scopeId = 0;
        
        //'core/config_data_collection'
        $collection = $this->_objectManager->create("Magento\Config\Model\ResourceModel\Config\Data\Collection");
        $collection->addFieldToFilter('scope', $scope);
        $collection->addFieldToFilter('scope_id', $scopeId);
        $collection->addFieldToFilter('path', $path . '/' . $type . '/' . $path);
        $collection->setPageSize(1);

        $v = $this->_objectManager->create('Magento\Framework\App\Config\Value');
        if (count($collection)){
            $v = $collection->getFirstItem();
        }
        else {
            $v->setScope($scope);
            $v->setScopeId($scopeId);
            $v->setPath($path . '/' . $type . '/' . $path);
        }

        return $v;
    }
	public function execute()
	{	
		$message = array();
		$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $currentrow = $this->_getNotCachedRow('currentrow', 0);
		if ($currentrow->getValue()!="") {
			$message['current_row'] = $currentrow->getValue();
		}
        $totalrow = $this->_getNotCachedRow('totalrow', 0);
        if ($totalrow->getValue()!="") {
            $message['total_row'] = $totalrow->getValue();
        }
        $importStatus = $this->_getNotCachedRow('importstatus', 0);
        if ($importStatus->getValue()!="") {
            $message['import_status'] = $importStatus->getValue();
        }
		$resultJson->setData($message);
		#session_write_close();// so ajax keeps running
		#flush();// so ajax keeps running
		return $resultJson;
	}
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'CommerceExtensions_ProductImportExport::import_export'
        );

    }
}	