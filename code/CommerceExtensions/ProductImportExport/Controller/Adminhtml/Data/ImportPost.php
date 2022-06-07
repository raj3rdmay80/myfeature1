<?php
/**
 * Copyright © 2019 CommerceExtensions. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CommerceExtensions\ProductImportExport\Controller\Adminhtml\Data;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use CommerceExtensions\ProductImportExport\Helper\Reset;

class ImportPost extends \CommerceExtensions\ProductImportExport\Controller\Adminhtml\Data
{

    /**
     * @var CommerceExtensions\ProductImportExport\Helper\Reset
     */
    protected $reset;

    /**
     * Constructor
     *
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param Reset $reset
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        Reset $reset

    ) {
    	$this->reset = $reset;
    	parent::__construct($context, $fileFactory);
    }

    /**
     * import action from import/export data
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
		$messagetoreturn=array();
		$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
		if ($this->getRequest()->isPost()){
			try {
				$params = $this->getRequest()->getParams();
				$this->saveProfileData($params);
				$importHandler = $this->_objectManager->create('CommerceExtensions\ProductImportExport\Model\Data\CsvImportHandler');
				if(isset($params['selectfilename'])) {
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
					$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
					$helper = $objectManager->get('CommerceExtensions\ProductImportExport\Helper\Data');
					$data = $resource->getTableName('productimportexport_uploadedfiledata') ;
					$rec = $resource->getConnection()->query("SELECT * FROM ".$data." WHERE file_name='".$params['selectfilename']."'");
					$request_list = $rec->fetchAll(); 
					//$data_one=array();
					$data_one=array_filter($request_list);
					if(isset($data_one[0])) {
						$filepath = $data_one[0]['file_uploaded_path'].'/'.$data_one[0]['file_name'];
						#$filepath = 'ProductImportExport/'.$params['selectfilename'];
						#$success = $this->messageManager->addSuccess(__('The Products have been imported Successfully.'));
						$memoryused = filesize($filepath);	
						session_write_close(); // so ajax keeps running
						flush();// so ajax keeps running
						$dataReturned = $importHandler->readCsvFile($filepath, $params);	
						
						if($dataReturned['import_status'] == "finished"){
							if ($helper->getStoreConfig('productimportexport/general/automaticallyreindex', 0)){
								$this->reindexdata();
							}
							$this->reset->setDefaultValue();
							$messagetoreturn['success'] = "The Products have been imported Successfully.";
							$messagetoreturn['total_rows'] = $dataReturned['total_rows'];
							$messagetoreturn['total_success_rows'] = $dataReturned['total_success_rows'];
							$messagetoreturn['totalwarnings'] = $dataReturned['warnings'];
							$messagetoreturn['memory_used'] = $memoryused;
						} else if($dataReturned['import_status'] == "canceled"){
							$messagetoreturn['success'] = "The Import Was Canceled";
							$messagetoreturn['total_rows'] = $dataReturned['total_rows'];
							$messagetoreturn['total_success_rows'] = $dataReturned['total_success_rows'];
							$messagetoreturn['totalwarnings'] = $dataReturned['warnings'];
							$messagetoreturn['memory_used'] = $memoryused;
						} else {
							$messagetoreturn['error'] = "The Products were NOT imported.";
							$messagetoreturn['total_rows'] = $dataReturned['total_rows'];
							$messagetoreturn['total_success_rows'] = $dataReturned['total_success_rows'];
							$messagetoreturn['totalwarnings'] = $dataReturned['warnings'];
							$messagetoreturn['memory_used'] = $memoryused;
						}
					} else {
						$messagetoreturn['error'] = "Invalid POST attempt file not found";
					}
				} else {
					//lets cancel the import / mark it finished
					$importStatus = $importHandler->_getNotCachedRow('importstatus', 0);
        			$importStatus->setValue("canceled")->save();
				}
				
			}
			catch (\Magento\Framework\Exception\LocalizedException $e) {
				$messagetoreturn['error'] = $e->getMessage();
			} 
			catch (\Exception $e) {
				$messagetoreturn['error'] = "Invalid file upload attempt: ". $e->getMessage();
			}
		} else {
			$messagetoreturn['error'] = "Invalid POST attempt file not found";
		}
		
		$resultJson->setData($messagetoreturn);
		return $resultJson;
				
    }
	
	public function saveProfileData($params){
	 
	 	$import_fields_mapped ="";
		if(isset($params['gui_data']['map']['product']['db']) && isset($params['gui_data']['map']['product']['file'])) {
			$mappedFieldsOnly = array_combine($params['gui_data']['map']['product']['db'],$params['gui_data']['map']['product']['file']);
			unset($mappedFieldsOnly[0]);
			$serializer = $this->_objectManager->create('\Magento\Framework\Serialize\Serializer\Json');
			$import_fields_mapped = $serializer->serialize($mappedFieldsOnly);
		}
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$resourceTable = $resource->getTableName('productimportexport_profiledata');
		$insData = array(
		'profile_type'=> 'import',
		'import_enclose'=> $params['import_enclose'],
		'import_delimiter'=> $params['import_delimiter'],
		'root_catalog_id'=> $params['root_catalog_id'],
		'enable_default_magento_format'=> $params['enable_default_magento_format'],
		'import_attribute_value'=> $params['import_attribute_value'],
		'attribute_for_import_value'=> $params['attribute_for_import_value'],
		'ref_by_product_id'=> $params['ref_by_product_id'],
		'create_products_only'=> $params['create_products_only'],
		'update_products_only'=> $params['update_products_only'],
		'import_images_by_url'=> $params['import_images_by_url'],
		'reimport_images'=> $params['reimport_images'],
		'deleteall_andreimport_images'=> $params['deleteall_andreimport_images'],
		'append_websites'=> $params['append_websites'],
		'append_tier_prices'=> $params['append_tier_prices'],
		'append_categories'=> $params['append_categories'],
		'append_grouped_products'=> $params['append_grouped_products'],
		'auto_create_categories'=> $params['auto_create_categories'],
		'import_fields'=> $params['import_fields'],
		'import_fields_mapped'=> $import_fields_mapped,
		);
		
		$rs = $resource->getConnection()->query("SELECT * FROM ".$resourceTable." WHERE profile_type = 'import'");
		$rows = $rs->fetchAll();
			
		if(count($rows)){
			$insData['update_time'] = date('Y-m-d H:i:s');
			$connection = $resource->getConnection()->update(''.$resourceTable.'', $insData, array('profile_type = ?'=> 'import'));
		} else {
			$insData['creation_time'] = date('Y-m-d H:i:s');
			$connection = $resource->getConnection()->insert(''.$resourceTable.'', $insData);
		}
	}
	
	public function reindexdata(){
		$Indexer = $this->_objectManager->create('Magento\Indexer\Model\Processor');
		$Indexer->reindexAll();
	}
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'CommerceExtensions_ProductImportExport::import_export'
        );
    }
}
