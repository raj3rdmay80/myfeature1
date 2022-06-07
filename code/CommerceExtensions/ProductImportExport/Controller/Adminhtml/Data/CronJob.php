<?php
/**
 * Copyright © 2020 CommerceExtensions. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CommerceExtensions\ProductImportExport\Controller\Adminhtml\Data;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Config\Model\Config\Backend\Image as SourceImage;

class CronJob extends \CommerceExtensions\ProductImportExport\Controller\Adminhtml\Data
{
	protected function writeToCsv($data) {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$directoryList = $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList');
		$csvProcessor = $objectManager->get('Magento\Framework\File\Csv');
		$fileDirectoryPath = $directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
	
		if(!is_dir($fileDirectoryPath))
			mkdir($fileDirectoryPath, 0777, true);
		$fileName = 'cron_log_errors.csv';
		$filePath =  $fileDirectoryPath . '/' . $fileName;
	
		#$data2 = [];
		  /* pass data array to write in csv file */
		#$data2 = [['column 1','column 2','column 3'],['100001','test','test2']];
		
		$csvProcessor
			->setEnclosure('"')
			->setDelimiter(',')
			->saveData($filePath, $data);
	
		return true;
	}
	public function execute()
	{
	 	if ($this->getRequest()->isPost()) {
			$params = $this->getRequest()->getParams();
			$Profile_type = $this->getRequest()->getPost('Profile_type');
			$Schedule = $this->getRequest()->getPost('Schedule');
			
			if($Schedule == '5minutes') {
				$ScheduleTime = '*/5 * * * *';
			} elseif($Schedule == 'Hourly') {
				$ScheduleTime = '0 * * * *';
			} elseif($Schedule == 'Every_2hrs') {
				$ScheduleTime = '0 */2 * * *';
			} elseif($Schedule == 'Every_24hrs') {
				$ScheduleTime = '0 23 * * *';
			} elseif($Schedule == 'Every_24hrs_at_3am') {
				$ScheduleTime = '0 0 3 * *';
			} else {
				$ScheduleTime = '0 0 * * 0'; 	
			}
			
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection();
			$core_config_data = $resource->getTableName('core_config_data');
					
			$request = $resource->getConnection()->query("SELECT * FROM ".$core_config_data);
			$request_list = $request->fetchAll();
	
			$import_result = array(); 
					
			foreach($request_list as $key => $value){
				if(in_array('crontab/default/jobs/CommerceExtensions/ProductImportExport/import_products/schedule/cron_expr', $request_list[$key]) || in_array('crontab/default/jobs/CommerceExtensions/ProductImportExport/export_products/schedule/cron_expr', $request_list[$key])){
					$import_result[] = array(
										'config_id' => $value['config_id'],	
										'path' => $value['path']
											);
				}							
			}
			
			if(!empty(array_key_exists('0', $import_result)) && empty(array_key_exists('1', $import_result))){
				$core_config_data = $resource->getTableName('core_config_data');	
				if(in_array('crontab/default/jobs/CommerceExtensions/ProductImportExport/import_products/schedule/cron_expr', $import_result['0']) && empty(array_key_exists('1', $import_result))){
						
						if($Profile_type == 'Import_Products'){
							$connection = $resource->getConnection()->update(''.$core_config_data.'', array('value'=> $ScheduleTime), 'config_id=' . $import_result['0']['config_id'] .''); 
						}
						if($Profile_type == 'Export_Products'){
							$hourly_array = array(
											'scope' => 'default',
											'scope_id' => '0',
											'path' => 'crontab/default/jobs/CommerceExtensions/ProductImportExport/export_products/schedule/cron_expr',
											'value' => $ScheduleTime
											);
							$connection = $resource->getConnection()->insert(''.$core_config_data.'', $hourly_array);
						}	
				}		
				
				if(in_array('crontab/default/jobs/CommerceExtensions/ProductImportExport/export_products/schedule/cron_expr', $import_result['0']) && empty(array_key_exists('1', $import_result))){
						if($Profile_type == 'Export_Products'){
							$connection = $resource->getConnection()->update(''.$core_config_data.'', array('value'=> $ScheduleTime), 'config_id=' . $import_result['0']['config_id'] .''); 	
						}
						
						if($Profile_type == 'Import_Products'){  
							$hourly_array = array(
											'scope' => 'default',
											'scope_id' => '0',
											'path' => 'crontab/default/jobs/CommerceExtensions/ProductImportExport/import_products/schedule/cron_expr',
											'value' => $ScheduleTime
												);
							$connection = $resource->getConnection()->insert(''.$core_config_data.'', $hourly_array);
						}
				}
						
			}
			if(count($import_result) == '0'){
			
				if($Profile_type == 'Export_Products'){
						$hourly_array = array(
										'scope' => 'default',
										'scope_id' => '0',
										'path' => 'crontab/default/jobs/CommerceExtensions/ProductImportExport/export_products/schedule/cron_expr',
										'value' => $ScheduleTime
											);
						$connection = $resource->getConnection()->insert(''.$core_config_data.'', $hourly_array);
				}
				if($Profile_type == 'Import_Products'){  
					$hourly_array = array(
									'scope' => 'default',
									'scope_id' => '0',
									'path' => 'crontab/default/jobs/CommerceExtensions/ProductImportExport/import_products/schedule/cron_expr',
									'value' => $ScheduleTime
										);
					$connection = $resource->getConnection()->insert(''.$core_config_data.'', $hourly_array);
				}
			}
			
			if(!empty(array_key_exists('0', $import_result)) && !empty(array_key_exists('1', $import_result))){
					 if($Profile_type == 'Import_Products'){
							$connection = $resource->getConnection()->update(''.$core_config_data.'', array('value'=> $ScheduleTime), 'config_id=' . $import_result['1']['config_id'] .''); 
						}
					if($Profile_type == 'Export_Products'){
							$connection = $resource->getConnection()->update(''.$core_config_data.'', array('value'=> $ScheduleTime), 'config_id=' . $import_result['0']['config_id'] .''); 	
					} 
					
			}
					
					$File_name = $this->getRequest()->getPost('import_file_name');
					$File_path = $this->getRequest()->getPost('import_file_path'); 
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
					$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
					$prefix = $resource->getTableName('productimportexport_cronjobdata');
					$insData = array();
					$insData = array(
					'root_catalog_id'=> $params['root_catalog_id'],
					'update_products_only'=> $params['update_products_only'],
					'ref_by_product_id'=> $params['ref_by_product_id'],
					'import_attribute_value'=> $params['import_attribute_value'],
					'attribute_for_import_value'=> $params['attribute_for_import_value'],
					'import_images_by_url'=> $params['import_images_by_url'],
					'reimport_images'=> $params['reimport_images'],
					'deleteall_andreimport_images'=> $params['deleteall_andreimport_images'],
					'append_tier_prices'=> $params['append_tier_prices'],
					'append_websites'=> $params['append_websites'],
					'append_grouped_products'=> $params['append_grouped_products'],
					'append_categories'=> $params['append_categories'],
					'auto_create_categories'=> $params['auto_create_categories'],
					'import_fields'=> $params['import_fields'],
					'export_fields'=> $params['export_fields'],
					'apply_additional_filters'=> $params['apply_additional_filters'],
					'filter_qty_from'=> $params['filter_qty_from'],
					'filter_qty_to'=> $params['filter_qty_to'],
					'product_id_from'=> $params['product_id_from'],
					'product_id_to'=> $params['product_id_to'],
					'export_grouped_position'=> $params['export_grouped_position'],
					'export_related_position'=> $params['export_related_position'],
					'export_crossell_position'=> $params['export_crossell_position'],
					'export_upsell_position'=> $params['export_upsell_position'],
					'export_category_paths'=> $params['export_category_paths'],
					'export_full_image_paths'=> $params['export_full_image_paths'],
					'export_multi_store'=> $params['export_multi_store'],
					'import_file_name'=> $params['import_file_name'],
					'import_file_path'=> $params['import_file_path'],
					'export_file_name'=> $params['export_file_name'],
					'export_file_path'=> $params['export_file_path'],
					'import_enclose'=> $params['import_enclose'],
					'import_delimiter'=> $params['import_delimiter'],
					'export_enclose'=> $params['export_enclose'],
					'export_delimiter'=> $params['export_delimiter'],
					'Profile_type'=> $params['Profile_type'],
					'Schedule'=> $params['Schedule'],
					'creation_time'=> date('Y-m-d H:i:s'),
					'data_transfer'=> $params['data_transfer'],
					'remote_file_name'=> $params['remote_file_name'],
					'remote_file_path'=> $params['remote_file_path'],
					'remote_host'=> $params['remote_host'],
					'remote_user_name'=> $params['remote_user_name'],
					'remote_password'=> $params['remote_password'],
					);
					
					$connection = $resource->getConnection()->insert(''.$prefix.'', $insData);
					//$rs = $resource->getConnection()->query("SELECT * FROM ".$prefix);	
					$this->_redirect($this->_redirect->getRefererUrl());
					$this->messageManager->addSuccess(__('Your "'.$params['Profile_type'].'" Cron Job Has Been Successfully Saved'));
		} else {
			
			$params = $this->getRequest()->getParams();
			
			if(isset($params['profileid']) && isset($params['deletereq'])) {
				if($params['deletereq'] && $params['profileid'] > 0) {
					//echo "DELETE PROFILE: " . $params['profileid'];
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
					$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
					$productimportexport_cronjobdata = $resource->getTableName('productimportexport_cronjobdata');
					$profile_id = $params['profileid'];
					$connection = $resource->getConnection()->delete($productimportexport_cronjobdata,['post_id = ?' => $profile_id]);
					//$rs = $resource->getConnection()->query("SELECT * FROM ".$prefix);	
					$this->_redirect($this->_redirect->getRefererUrl());
					$this->messageManager->addSuccess(__('Profile ID: "'.$profile_id.'" hHas Been Successfully Deleted'));
				}
			}
			$cronLogErrors[] = array('cronjob','error');
			$cronLogErrors[] = array("import_products", "Begin Cron Import");
			
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$prefix = $resource->getTableName('productimportexport_cronjobdata');
			$core_config_data = $resource->getTableName('core_config_data');
							
			$request = $resource->getConnection()->query("SELECT * FROM ".$core_config_data);
			$request_list = $request->fetchAll();
				
			foreach($request_list as $key => $value){
				if(in_array('crontab/default/jobs/CommerceExtensions/ProductImportExport/import_products/schedule/cron_expr', $request_list[$key]) || in_array('crontab/default/jobs/CommerceExtensions/ProductImportExport/export_products/schedule/cron_expr', $request_list[$key])){
					$import_result[] = array('config_id' => $value['config_id'], 'path' => $value['path']);
					$cronLogErrors[] = array("import_products", "We Got A Cron To Run -- > CONFIG ID: " . $value['config_id'] . " PATH --> " . $value['path']);	
				}							
			}
			// Send the fetched array for import functionality 
			foreach($import_result as $key => $value){
				if(in_array('crontab/default/jobs/CommerceExtensions/ProductImportExport/import_products/schedule/cron_expr', $import_result[$key])){	
					$cronLogErrors[] = array("import_products", "Run Cronjob_import()");
					$rs = $resource->getConnection()->query("SELECT * FROM ".$prefix . " WHERE Profile_type = 'Import_Products'");	
					$rows = $rs->fetchAll();
					$this->Cronjob_import($rows[$rowCount], $cronLogErrors);
					$rowCount++;
				}	
			}	 
		}				
	}
		
	// For Import 
	public function Cronjob_import($params, $cronLogErrors){
	
		try {
		
			$cronLogErrors[] = array("import_products", "Start Running Cronjob_import() Import");
			#$importHandler = $this->_objectManager->create('CommerceExtensions\ProductImportExport\Model\Data\CsvImportHandler');  
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$importHandler = $objectManager->create('CommerceExtensions\ProductImportExport\Model\Data\CsvImportHandler'); 
			
			$file_path = $params['import_file_path']; //$cronLogErrors[] = array("import_products", "File_path: " . $File_path);
			$file_name = $params['import_file_name']; //$cronLogErrors[] = array("import_products", "File_name: " . $File_name);
			#$url = $file_path.'/'.$file_name; //$cronLogErrors[] = array("import_products", "Before file_get_contents() FILE: " . $url);
			$url = $file_path . DIRECTORY_SEPARATOR . $file_name; //$cronLogErrors[] = array("import_products", "Before file_get_contents() FILE: " . $url);
			#$tempName = tempnam('/tmp', 'php_files'); //$cronLogErrors[] = array("import_products", "Before file_get_contents() tempName: " . $tempName);
			#$csvRawData = file_get_contents($url); //$cronLogErrors[] = array("import_products", "Before file_put_contents() Size: " . strlen($csvRawData));
			#file_put_contents($tempName, $csvRawData);
			
			//THIS NEEDS TO BE HERE FIRST INCASE var/ProductImportExport doesn't exist and it wants to save it via remote ftp
			$ioFile = $objectManager->get('Magento\Framework\Filesystem\Io\File');
            $importDir = $importHandler->getVarDirProductImportExportDir();
            $ioFile->checkAndCreateFolder($importDir);
			
			//REMOTE FTP 
			if(isset($params['data_transfer'])) {
				if($params['data_transfer']=="remote") {
					$ftp_server = $params['remote_host'];
					$ftp_user = $params['remote_user_name'];
					$ftp_pass = $params['remote_password'];
					$ftp_path = $params['remote_file_path'];
					$ftp_file_name = $params['remote_file_name'];
					
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
					$directoryList = $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList');
					$fileDirectoryPath = $directoryList->getRoot();
					$localFilePath =  $fileDirectoryPath . '/' . $file_path;
		
					// set up basic connection
					$conn_id = ftp_connect($ftp_server);
					
					// login with username and password
					$login_result = ftp_login($conn_id, $ftp_user, $ftp_pass);
					ftp_pasv($conn_id, true).
					ftp_chdir($conn_id, $ftp_path); 
					//$ftp_nlist = ftp_nlist($conn_id, ".");
					$_remoteFile = $ftp_file_name;
					$cronLogErrors[] = array("import_products", "REMOTE FILE PATH: ". $_remoteFile);
					//$this->writeToCsv($cronLogErrors);
					//foreach ($ftp_nlist as $_remoteFile) {
					  //2. Size is not '-1' => file
					  //if (!(ftp_size($conn_id, $_remoteFile) == -1)) {
						  //output as file
						  // download a file
						  $saveFile = $localFilePath . '/' . $file_name;
						  $cronLogErrors[] = array("import_products", "LOCAL FILE PATH: ". $saveFile);
						  //$this->writeToCsv($cronLogErrors);
						  if (ftp_get($conn_id, $saveFile, $_remoteFile, FTP_BINARY)) {
							$cronLogErrors[] = array("import_products", "Successfully transfered via FTP ===> ". $_remoteFile);
							$this->writeToCsv($cronLogErrors);
							//$IMPORT_FILE = $_remoteFile;
						  } else {
							$cronLogErrors[] = array("import_products", "There was a problem while fetching remote file: ". $_remoteFile);
							$this->writeToCsv($cronLogErrors);
						  }
					  //}
					//}
					// close the connection
					ftp_close($conn_id);
				}
			}
			//REMOTE FTP
			
            #$filepath = $importDir . '/' . $file_name;
			$filepath = $importDir . DIRECTORY_SEPARATOR . $file_name;
			$result = $ioFile->read($url, $filepath);
			
			$param1 = array(
			'import_delimiter' => $params['import_delimiter'],
			'import_enclose' => $params['import_enclose'],
			'root_catalog_id'=> $params['root_catalog_id'],
			'ref_by_product_id'=> $params['ref_by_product_id'],
			'import_attribute_value'=> $params['import_attribute_value'],
			'attribute_for_import_value'=> $params['attribute_for_import_value'],
			'create_products_only'=> $params['create_products_only'],
			'update_products_only'=> $params['update_products_only'],
			'import_images_by_url'=> $params['import_images_by_url'],
			'reimport_images'=> $params['reimport_images'],
			'deleteall_andreimport_images'=> $params['deleteall_andreimport_images'],
			'append_tier_prices'=> $params['append_tier_prices'],
			'append_grouped_products'=> $params['append_grouped_products'],
			'append_categories'=> $params['append_categories'],
			'auto_create_categories'=> $params['auto_create_categories'],
			'append_websites'=> $params['append_websites']
			);
						
			$cronLogErrors[] = array("import_products", "Before readCsvFile() Import");
			try {		
				$dataReturned = $importHandler->readCsvFile($filepath, $param1);
			}
			catch (\Magento\Framework\Exception\LocalizedException $e) {
				$cronLogErrors[] = array("import_products", "ERROR: " . $e->getMessage());
				$this->writeToCsv($cronLogErrors);	
				//$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$cronLogErrors[] = array("import_products", "Invalid file upload attempt" . $e->getMessage());
				$this->writeToCsv($cronLogErrors);	
				//$this->messageManager->addError(__('Invalid file upload attempt' . $e->getMessage()));
			} 
			$cronLogErrors[] = array("import_products", "After readCsvFile() Import");
			$cronLogErrors[] = array("import_products", "Read File");
			$this->writeToCsv($cronLogErrors);	
			if(isset($dataReturned['import_status'])) {		
				if($dataReturned['import_status'] == "finished"){
					$cronLogErrors[] = array("import_products", "Products have been imported Successfully");
					$this->writeToCsv($cronLogErrors);	
					$this->reindexdata();
					$this->messageManager->addSuccess(__('The Products have been imported Successfully.'));
				} else {
					$cronLogErrors[] = array("import_products", "Error The Products were NOT imported.");
					$this->writeToCsv($cronLogErrors);	
					$this->messageManager->addError("The Products were NOT imported.");
					$this->messageManager->addError("TOTAL ROWS: " . $dataReturned['total_rows']);
					$this->messageManager->addError("WARNINGS: " . $dataReturned['warnings']);
				}
			} else {
				$cronLogErrors[] = array("import_products", "Error The Products were NOT imported.");
				$this->writeToCsv($cronLogErrors);	
				$this->messageManager->addError("The Products were NOT imported.");
			}
			//$this->_redirect($this->_redirect->getRefererUrl()); 
		
		}
		catch (\Magento\Framework\Exception\LocalizedException $e) {
			$this->messageManager->addError($e->getMessage());
		} catch (\Exception $e) {
			$this->messageManager->addError(__('Invalid file upload attempt' . $e->getMessage()));
		} 
					
	}
	
	public function reindexdata(){
		$Indexer = $this->_objectManager->create('Magento\Indexer\Model\Processor');
		$Indexer->reindexAll();
	}

}	