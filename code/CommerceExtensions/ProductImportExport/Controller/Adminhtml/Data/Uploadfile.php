<?php
/**
 * Copyright © 2017 CommerceExtensions. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CommerceExtensions\ProductImportExport\Controller\Adminhtml\Data;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class Uploadfile extends \CommerceExtensions\ProductImportExport\Controller\Adminhtml\Data
{	
	/**
     * import action from import/export data
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
	public function execute()
	{	
		$arraytoinsert = array();
		$arraytoinsert = $this->getRequest()->getFiles('file');
		$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		
		$data = $resource->getTableName('productimportexport_uploadedfiledata') ;
		$rec = $resource->getConnection()->query("SELECT * FROM ".$data." WHERE file_name='".$arraytoinsert['name']."'");
		$request_list = $rec->fetchAll(); 
		$data_one=array_filter($request_list);
		
		if(!empty($data_one)){
		
			$importHandler = $this->_objectManager->create('CommerceExtensions\ProductImportExport\Model\Data\CsvImportHandler');  
			$readData = $importHandler->UploadCsvOfproduct($this->getRequest()->getFiles('file'));
			$sql = $resource->getConnection()->query("UPDATE ".$data." SET file_name ='".$readData['file']."', file_type='".$arraytoinsert['type']."', file_size='".$arraytoinsert['size']."', file_uploaded_path='".$readData['path']."' WHERE post_id='".$data_one['0']['post_id']."'");
			$resultJson->setData($readData['file']);
			
		} else {
		
			$importHandler = $this->_objectManager->create('CommerceExtensions\ProductImportExport\Model\Data\CsvImportHandler');  
			$readData = $importHandler->UploadCsvOfproduct($this->getRequest()->getFiles('file'));
			$sql = $resource->getConnection()->query("INSERT INTO ".$data."(`file_name` ,`file_type`,`file_size`,`file_uploaded_path`) VALUES ('".$readData['file']."','".$arraytoinsert['type']."','".$arraytoinsert['size']."','".$readData['path']."');");	
			$resultJson->setData($readData['file']);
						
		} 
		return $resultJson;
	}
}	