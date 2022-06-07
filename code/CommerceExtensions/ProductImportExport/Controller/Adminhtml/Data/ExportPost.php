<?php
/**
 * Copyright Â© 2015 CommerceExtensions. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CommerceExtensions\ProductImportExport\Controller\Adminhtml\Data;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
#use Magento\Catalog\Model\Product;
#use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class ExportPost extends \CommerceExtensions\ProductImportExport\Controller\Adminhtml\Data
{
    /**
     * Export action from import/export data
     *
     * @return ResponseInterface
     */
	const MULTI_DELIMITER = ' , ';
	
	/* REMOVE ATTRIBUTES WE DO NOT WANT TO EXPORT */ 
    protected $_systemFields = ['price','weight','special_price','cost','msrp','sku','attribute_set_id','entity_id','type_id','has_options','required_options','name','swatch_image','image','small_image','thumbnail','url_key','meta_title','meta_description','meta_keyword','image_label','small_image_label','thumbnail_label','short_description','description','created_at','updated_at','special_from_date','special_to_date','custom_design_from','custom_design_to','news_from_date','news_to_date','custom_layout_update'];
	
    protected $_disabledAttributes = ['attribute_set_id','tier_price','entity_id','old_id','media_gallery','sku_type','weight_type','shipment_type','price_type','groupprice'];
	
    protected $_attributes = array();
	
	 /**
     * Prepare products media gallery
     *
     * @param  int[] $productIds
     * @return array 
     */
	protected $_category;
    protected $_categoryFactory;
	protected $resourceConnection;
	protected $fileFactory;
	protected $frameworkUrl;
	protected $storeManager;
	protected $stockRegistry;
	protected $taxClassModel;
	protected $productModel;
	protected $configurableProduct;
	protected $downloadableLink;
	protected $downloadableSample;
	protected $bundleOption;
	protected $bundleSelection;
	protected $attributeSet;
    protected $productAttributeCollection;
	protected $ProductMetadataInterface;
	

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
		\Magento\Framework\Module\Manager $moduleManager,
		\Magento\Framework\App\ProductMetadataInterface $ProductMetadataInterface,
		\Magento\Framework\Url $frameworkUrl,
		\Magento\Store\Model\StoreManager $storeManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\CatalogInventory\Model\StockRegistry $stockRegistry,
		\Magento\Tax\Model\ClassModel $taxClassModel,
        \Magento\Catalog\Model\Product $productModel,
		\Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProduct,
		\Magento\Downloadable\Model\Link $downloadableLink,
		\Magento\Downloadable\Model\Sample $downloadableSample,
		\Magento\Bundle\Model\Option $bundleOption,
		\Magento\Bundle\Model\Selection $bundleSelection,
		\Magento\Eav\Model\Entity\Attribute\Set $attributeSet,
		\Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $productAttributeCollection,
		\Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->fileFactory = $fileFactory;
        $this->moduleManager = $moduleManager;
        $this->_productMetadataInterface = $ProductMetadataInterface;
        $this->_frameworkUrl = $frameworkUrl;
        $this->_storeManager = $storeManager;
        $this->_categoryFactory = $categoryFactory;  
        $this->_stockRegistry = $stockRegistry;
        $this->_taxClassModel = $taxClassModel;
        $this->_productModel = $productModel;
        $this->_configurableProduct = $configurableProduct;
        $this->_downloadableLink = $downloadableLink;
        $this->_downloadableSample = $downloadableSample;
        $this->_bundleOption = $bundleOption;
		$this->_bundleSelection = $bundleSelection;
		$this->_attributeSet = $attributeSet;
        $this->_productAttributeCollection = $productAttributeCollection;
        $this->directoryList = $directoryList;
		parent::__construct($context,$fileFactory);
    }
	
    protected function getMediaGallery(array $productIds)
    {
        if (empty($productIds)) {
            return [];
        }
		$_resource = $this->resourceConnection;
		$connection = $_resource->getConnection();
		
		if($this->_productMetadataInterface->getEdition() == "Community") {
			$select = $connection->select()->from(
				['mg' => $_resource->getTableName('catalog_product_entity_media_gallery')],
				[
					'mg.value_id',
					'mg.attribute_id',
					'filename' => 'mg.value',
					'mgv.label',
					'mgv.position',
					'mgv.disabled'
				]
			)->joinLeft(
				['mgv' => $_resource->getTableName('catalog_product_entity_media_gallery_value')],
				'(mg.value_id = mgv.value_id AND mgv.store_id = 0)',
				[]
			)->where(
				'mgv.entity_id IN(?)', //was 'mg.value_id IN(?)' or mgv.value_id IN(?) or 2.1.7 > entity_id to row_id
				$productIds
			);
		} else {
			$select = $connection->select()->from(
				['mg' => $_resource->getTableName('catalog_product_entity_media_gallery')],
				[
					'mg.value_id',
					'mg.attribute_id',
					'filename' => 'mg.value',
					'mgv.label',
					'mgv.position',
					'mgv.disabled'
				]
			)->joinLeft(
				['mgv' => $_resource->getTableName('catalog_product_entity_media_gallery_value')],
				'(mg.value_id = mgv.value_id AND mgv.store_id = 1)',
				[]
			)->where(
				'mgv.row_id IN(?)', //EE 2.1.7 > entity_id to row_id
				$productIds
			);
		}
        $rowMediaGallery = [];
        $stmt = $connection->query($select);
        while ($mediaRow = $stmt->fetch()) {
            $rowMediaGallery[] = [
                '_media_attribute_id' => $mediaRow['attribute_id'],
                '_media_image' => $mediaRow['filename'],
                '_media_label' => $mediaRow['label'],
                '_media_position' => $mediaRow['position'],
                '_media_is_disabled' => $mediaRow['disabled'],
            ];
        }

        return $rowMediaGallery;
    }

    public function getCategory($categoryId) 
    {
        $this->_category = $this->_categoryFactory->create();
        $this->_category->load($categoryId);        
        return $this->_category;
    }
	
	public function getVersion()
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$magentoVersion = $objectManager->create('Magento\Framework\App\ProductMetadataInterface');
        return $magentoVersion->getVersion();
    }
	
	public function saveProfileData($params){
	 
	 	$export_fields_mapped ="";
		if(isset($params['gui_data']['map']['product']['db']) && isset($params['gui_data']['map']['product']['file'])) {
			$mappedFieldsOnly = array_combine($params['gui_data']['map']['product']['db'],$params['gui_data']['map']['product']['file']);
			unset($mappedFieldsOnly[0]);
			$serializer = $this->_objectManager->create('\Magento\Framework\Serialize\Serializer\Json');
			$export_fields_mapped = $serializer->serialize($mappedFieldsOnly);
		}
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$resourceTable = $resource->getTableName('productimportexport_profiledata');
		$insData = array(
		'profile_type'=> 'export',
		'export_delimiter'=> $params['export_delimiter'],
		'export_enclose'=> $params['export_enclose'],
		'export_manual_file_name'=> $params['export_manual_file_name'],
		'export_fields'=> $params['export_fields'],
		'export_fields_mapped'=> $export_fields_mapped,
		'product_id_from'=> $params['product_id_from'],
		'product_id_to'=> $params['product_id_to'],
		'apply_additional_filters'=> $params['apply_additional_filters'],
		'export_filter_by_attribute_code'=> $params['export_filter_by_attribute_code'],
		'export_filter_by_attribute_value'=> $params['export_filter_by_attribute_value'],
		'filter_qty_from'=> $params['filter_qty_from'],
		'filter_qty_to'=> $params['filter_qty_to'],
		'export_filter_by_categoryids'=> $params['export_filter_by_categoryids'],
		'export_filter_by_skus'=> $params['export_filter_by_skus'],
		'export_filter_by_product_type'=> $params['export_filter_by_product_type'],
		'filter_status'=> $params['filter_status'],
		'export_filter_by_attribute_set'=> $params['export_filter_by_attribute_set'],
		'export_multi_store'=> $params['export_multi_store'],
		'export_grouped_position'=> $params['export_grouped_position'],
		'export_related_position'=> $params['export_related_position'],
		'export_crossell_position'=> $params['export_crossell_position'],
		'export_upsell_position'=> $params['export_upsell_position'],
		'export_category_paths'=> $params['export_category_paths'],
		'export_full_image_paths'=> $params['export_full_image_paths'],
		);
		
		$rs = $resource->getConnection()->query("SELECT * FROM ".$resourceTable." WHERE profile_type = 'export'");
		$rows = $rs->fetchAll();
			
		if(count($rows)){
			$insData['update_time'] = date('Y-m-d H:i:s');
			$connection = $resource->getConnection()->update(''.$resourceTable.'', $insData, array('profile_type = ?'=> 'export'));
		} else {
			$insData['creation_time'] = date('Y-m-d H:i:s');
			$connection = $resource->getConnection()->insert(''.$resourceTable.'', $insData);
		}
	}
	
    public function execute()
    {				
		// Export functionality from Export  Products button and through cron job 
		$params = $this->getRequest()->getParams();
		
		if(count($params) >= '24'){
		
			$this->saveProfileData($params);
			$this->export_functionality_from_export_form($params,$cronjob_export_path="",$cronjob_export_name="");	
		
		} else {
		
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection();
			$prefix = $resource->getTableName('productimportexport_cronjobdata');
			
			$core_config_data = $resource->getTableName('core_config_data');
			$request = $resource->getConnection()->query("SELECT * FROM ".$core_config_data);
			$request_list = $request->fetchAll();
				
			foreach($request_list as $key => $value){
				if(in_array('crontab/default/jobs/CommerceExtensions/ProductImportExport/import_products/schedule/cron_expr', $request_list[$key]) || in_array('crontab/default/jobs/CommerceExtensions/ProductImportExport/export_products/schedule/cron_expr', $request_list[$key])){
					$import_result[] = array(
											'config_id' => $value['config_id'],	
											'path' => $value['path']
											);
				}							
			}
			
			$rowCount=0;	 
			foreach($import_result as $key => $value){
				if(in_array('crontab/default/jobs/CommerceExtensions/ProductImportExport/export_products/schedule/cron_expr', $import_result[$key])){	
				
					$rs = $resource->getConnection()->query("SELECT * FROM ".$prefix." WHERE Profile_type = 'Export_Products'");	
					$rows = $rs->fetchAll();
					$export = array(
						'export_delimiter'=> $rows[$rowCount]['export_delimiter'],
						'export_enclose'=> $rows[$rowCount]['export_enclose'],
						'export_fields'=> $rows[$rowCount]['export_fields'],
						'apply_additional_filters'=> $rows[$rowCount]['apply_additional_filters'],
						'filter_qty_from'=> $rows[$rowCount]['filter_qty_from'],
						'filter_qty_to'=> $rows[$rowCount]['filter_qty_to'],
						'filter_status'=> $rows[$rowCount]['filter_status'],
						'product_id_from'=> $rows[$rowCount]['product_id_from'],
						'product_id_to'=> $rows[$rowCount]['product_id_to'],
						'export_grouped_position'=> $rows[$rowCount]['export_grouped_position'],
						'export_related_position'=> $rows[$rowCount]['export_related_position'],
						'export_crossell_position'=> $rows[$rowCount]['export_crossell_position'],
						'export_upsell_position'=> $rows[$rowCount]['export_upsell_position'],
						'export_category_paths'=> $rows[$rowCount]['export_category_paths'],
						'export_full_image_paths'=> $rows[$rowCount]['export_full_image_paths'],
						'export_multi_store'=> $rows[$rowCount]['export_multi_store']
					);
					$cronjob_export_path = $rows[$rowCount]['export_file_path'];
					$cronjob_export_name = $rows[$rowCount]['export_file_name'];
					$this->export_functionality_from_export_form($export,$cronjob_export_path,$cronjob_export_name);
					$rowCount++;
				}	
			}	 	
		} 
	}
	
	public function	export_functionality_from_export_form($params,$cronjob_export_path,$cronjob_export_name){
			
		$_resource = $this->resourceConnection;
		$catalog_category_product = $_resource->getTableName('catalog_category_product');
		$catalog_product_bundle_option_value = $_resource->getTableName('catalog_product_bundle_option_value');
		$_productData = $this->_productModel;
		$_productAttributes = $this->_productAttributeCollection->load();
		$_stockData = $this->_stockRegistry;
		$connection = $_resource->getConnection();
		
		if($params['export_delimiter'] != "") {
			$delimiter = $params['export_delimiter'];
		} else {
			$delimiter = ",";
		}
		if($params['export_enclose'] != "") {
			$enclose = $params['export_enclose'];
		} else {
			$enclose = "\"";
		}
		
		if($params['export_fields'] == "false") {
			$template = "";
			$attributesArray = array();
			foreach ($params['gui_data']['map']['product']['db'] as $mappedField) {
				if($mappedField != "0") {
					$template .= ''.$enclose.'{{'.$mappedField.'}}'.$enclose.''.$delimiter.'';
					$attributesArray[$mappedField] = $mappedField;
				}
			}
		
		} else {
			/* BUILD OUT COLUMNS NOT IN DEFAULT ATTRIBUTES */
			$template = ''.$enclose.'{{store}}'.$enclose.''.$delimiter.''.$enclose.'{{websites}}'.$enclose.''.$delimiter.''.$enclose.'{{attribute_set}}'.$enclose.''.$delimiter.''.$enclose.'{{prodtype}}'.$enclose.''.$delimiter.''.$enclose.'{{related}}'.$enclose.''.$delimiter.''.$enclose.'{{upsell}}'.$enclose.''.$delimiter.''.$enclose.'{{crosssell}}'.$enclose.''.$delimiter.''.$enclose.'{{tier_prices}}'.$enclose.''.$delimiter.''.$enclose.'{{associated}}'.$enclose.''.$delimiter.''.$enclose.'{{config_attributes}}'.$enclose.''.$delimiter.''.$enclose.'{{bundle_options}}'.$enclose.''.$delimiter.''.$enclose.'{{bundle_selections}}'.$enclose.''.$delimiter.''.$enclose.'{{grouped}}'.$enclose.''.$delimiter.''.$enclose.'{{group_price_price}}'.$enclose.''.$delimiter.''.$enclose.'{{downloadable_options}}'.$enclose.''.$delimiter.''.$enclose.'{{downloadable_sample_options}}'.$enclose.''.$delimiter.''.$enclose.'{{gallery_label}}'.$enclose.''.$delimiter.''.$enclose.'{{qty}}'.$enclose.''.$delimiter.''.$enclose.'{{min_qty}}'.$enclose.''.$delimiter.''.$enclose.'{{use_config_min_qty}}'.$enclose.''.$delimiter.''.$enclose.'{{is_qty_decimal}}'.$enclose.''.$delimiter.''.$enclose.'{{backorders}}'.$enclose.''.$delimiter.''.$enclose.'{{use_config_backorders}}'.$enclose.''.$delimiter.''.$enclose.'{{min_sale_qty}}'.$enclose.''.$delimiter.''.$enclose.'{{use_config_min_sale_qty}}'.$enclose.''.$delimiter.''.$enclose.'{{max_sale_qty}}'.$enclose.''.$delimiter.''.$enclose.'{{use_config_max_sale_qty}}'.$enclose.''.$delimiter.''.$enclose.'{{is_in_stock}}'.$enclose.''.$delimiter.''.$enclose.'{{low_stock_date}}'.$enclose.''.$delimiter.''.$enclose.'{{notify_stock_qty}}'.$enclose.''.$delimiter.''.$enclose.'{{use_config_notify_stock_qty}}'.$enclose.''.$delimiter.''.$enclose.'{{manage_stock}}'.$enclose.''.$delimiter.''.$enclose.'{{use_config_manage_stock}}'.$enclose.''.$delimiter.''.$enclose.'{{stock_status_changed_auto}}'.$enclose.''.$delimiter.''.$enclose.'{{use_config_qty_increments}}'.$enclose.''.$delimiter.''.$enclose.'{{qty_increments}}'.$enclose.''.$delimiter.''.$enclose.'{{enable_qty_increments}}'.$enclose.''.$delimiter.''.$enclose.'{{is_decimal_divided}}'.$enclose.''.$delimiter.''.$enclose.'{{use_config_enable_qty_increments}}'.$enclose.''.$delimiter.''.$enclose.'{{use_config_enable_qty_inc}}'.$enclose.''.$delimiter.''.$enclose.'{{stock_status_changed_automatically}}'.$enclose.''.$delimiter.''.$enclose.'{{product_id}}'.$enclose.''.$delimiter.''.$enclose.'{{store_id}}'.$enclose.''.$delimiter.''.$enclose.'{{additional_attributes}}'.$enclose.''.$delimiter.'';
			
			$attributesArray = array('store' => 'store', 'websites' => 'websites', 'attribute_set' => 'attribute_set', 'prodtype' => 'prodtype', 'related' => 'related', 'upsell' => 'upsell', 'crosssell' => 'crosssell', 'tier_prices' => 'tier_prices', 'associated' => 'associated', 'config_attributes' => 'config_attributes', 'bundle_options' => 'bundle_options', 'bundle_selections' => 'bundle_selections', 'grouped' => 'grouped', 'group_price_price' => 'group_price_price', 'downloadable_options' => 'downloadable_options', 'downloadable_sample_options' => 'downloadable_sample_options', 'gallery_label' => 'gallery_label', 'qty' => 'qty', 'min_qty' => 'min_qty', 'use_config_min_qty' => 'use_config_min_qty', 'is_qty_decimal' => 'is_qty_decimal', 'backorders' => 'backorders', 'use_config_backorders' => 'use_config_backorders', 'min_sale_qty' => 'min_sale_qty', 'use_config_min_sale_qty' => 'use_config_min_sale_qty', 'max_sale_qty' => 'max_sale_qty', 'use_config_max_sale_qty' => 'use_config_max_sale_qty', 'is_in_stock' => 'is_in_stock', 'low_stock_date' => 'low_stock_date', 'notify_stock_qty' => 'notify_stock_qty', 'use_config_notify_stock_qty' => 'use_config_notify_stock_qty', 'manage_stock' => 'manage_stock', 'use_config_manage_stock' => 'use_config_manage_stock', 'stock_status_changed_auto' => 'stock_status_changed_auto', 'use_config_qty_increments' => 'use_config_qty_increments', 'qty_increments' => 'qty_increments', 'enable_qty_increments' => 'enable_qty_increments', 'is_decimal_divided' => 'is_decimal_divided', 'use_config_enable_qty_increments' => 'use_config_enable_qty_increments', 'use_config_enable_qty_inc' => 'use_config_enable_qty_inc', 'stock_status_changed_automatically' => 'stock_status_changed_automatically', 'product_id' => 'product_id', 'store_id' => 'store_id', 'additional_attributes' => 'additional_attributes');
		
		
			if($params['export_category_paths'] == "true") {
				$template .= ''.$enclose.'{{categories}}'.$enclose.''.$delimiter.'';
				$attributesArray = array_merge($attributesArray, array('categories' => 'categories'));
			}
			#$product_stock_attributes = $_stockData->getStockItem(2);
			foreach ($_productAttributes as $productAttr) {
				$col = $productAttr->getAttributeCode();
				if (!in_array($col, $this->_disabledAttributes)) {
					$attributesArray[$col] = $col;
					$template .= ''.$enclose.'{{'.$col.'}}'.$enclose.''.$delimiter.'';
				}
			}
			if ($this->moduleManager->isEnabled('MageWorx_OptionBase')) {
				$template .= ''.$enclose.'{{enable_absolute_price}}'.$enclose.''.$delimiter.''.$enclose.'{{enable_sku_policy}}'.$enclose.''.$delimiter.'';
				$attributesArray = array_merge($attributesArray, array('enable_absolute_price' => 'enable_absolute_price','enable_sku_policy' => 'enable_sku_policy'));
			}
		
		}
		
		if($params['product_id_from'] != "" && $params['product_id_to'] != "" && $params['apply_additional_filters'] != "yes_additional_filters") {
			$productCollection = $_productData->getCollection()
									->addAttributeToSelect('*')
									->addAttributeToFilter ( 'entity_id' , array( "from" => $params['product_id_from'], "to" => $params['product_id_to'] ))
									->load();	
		} else if($params['apply_additional_filters'] == "yes_additional_filters") {	
			
			$productCollection = $_productData->getCollection();
			
			if($params['product_id_from'] != "" && $params['product_id_to'] != "") {
				$productCollection->addAttributeToFilter ( 'entity_id' , array( "from" => $params['product_id_from'], "to" => $params['product_id_to'] ));
			} 
			
			if($params['export_filter_by_attribute_code'] != "") {	
				$attribute_code = $params['export_filter_by_attribute_code'];
				$attribute_value = $params['export_filter_by_attribute_value'];
				$productCollection->addAttributeToSelect('*')->addAttributeToSelect($attribute_code);
				
				$productCollection->addFieldToFilter(array(array('attribute'=>$attribute_code,'eq'=> $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product')
								->getAttribute($attribute_code)
								->getSource()
								->getOptionId(trim($attribute_value)))))->load();
			}
			
			if($params['filter_qty_from'] !="" && $params['filter_qty_to'] !="") {
				#$entityIds_cat_filters->addAttributeToFilter('attribute_set_id',  $this->getVar('filter/attribute_set'));
				$cataloginventory_stock_item = $_resource->getTableName('cataloginventory_stock_item');
				$productCollection->addAttributeToSelect('*')
								  ->joinField('qty',
									 $cataloginventory_stock_item,
									 'qty',
									 'product_id=entity_id',
									 '{{table}}.stock_id=1',
									 'left'
								  );
				$productCollection->addAttributeToFilter('qty' , array( "from" => $params['filter_qty_from'], "to" => $params['filter_qty_to']));
			}
			
			if($params['filter_status'] != "0") {	
				$productCollection->addAttributeToFilter('status' , array("eq" => $params['filter_status']));
			}
			if($params['export_filter_by_categoryids'] != "") {
				$catids = explode(",", $params['export_filter_by_categoryids']);	
				$statements = array();
				foreach ($catids as $categoryId){
					if (is_numeric($categoryId)){
					 $statements[] = "{{table}}.category_id = $categoryId";
					}
				}
				$productCollection->distinct(true)
				->joinField('category_id',$catalog_category_product, null,'product_id = entity_id', implode(" OR ",$statements),'inner');
			}
			if($params['export_filter_by_attribute_set'] !="0") {
				$productCollection->addAttributeToFilter('attribute_set_id' , array("eq" => $params['export_filter_by_attribute_set']));
			}
			if($params['export_filter_by_skus'] != "") {
				$product_skus = explode(",",$params['export_filter_by_skus']);	
				$productCollection->addAttributeToFilter('sku' , array("in" => array($product_skus)));
			}		
			if($params['export_filter_by_product_type'] !="0") {
				$productCollection->addAttributeToFilter('type_id', array('eq' => $params['export_filter_by_product_type']));
			}
			/*		
			if($this->getVar('filter/visibility')!="") {
				$entityIds->addAttributeToFilter('visibility', array('eq' => $this->getVar('filter/visibility')));
			}
			*/
		  
		} else {
			$productCollection = $_productData->getCollection()
									->addAttributeToSelect('*')
									->load();
		}
		
		$Custdata = array();
		if($params['export_fields'] != "false") {
			foreach($productCollection as $product){
				
				$_prodModel = $this->_objectManager->create('Magento\Catalog\Model\Product')->setStoreId(0)->load($product->getId());
				if(is_array($_prodModel->getOptions())) {
					foreach ($_prodModel->getOptions() as $o) {
						if(!empty($o->getData())){
							
							if ($this->moduleManager->isEnabled('MageWorx_OptionBase')) {
						
							$finaloptiondescription = $o->getData('description');
							//defaults
							$oDelimiter   		= "__"; //"[]"
							$price_type			= "fixed"; 
							$price				= "0.0000"; 
							$sku 				= " ";
							$special_price		= "0.00";
							$special_comment	= " ";
							$sort_order 		= "0";
							$customoptions_qty  = "0";
							$max_characters		= "0";
							$imagesdata			= array();
							$in_group_id		= "0";
							$tier_pricing 		= ""; 
							$dependent_ids		= "";
							$image_path			= "";
							$image_color		= "";
							//defaults
							
							//m1 to m2 mapping
							//view_mode = disabled	
							//is_dependent = dependency_type
							//in_group_id = group_option_id
							//customoptions_is_onetime = one_time
							//image_mode = mageworx_option_image_mode
							//customer_groups = gone in m2??
							
							$customoptionstitle = str_replace(" ", "_", str_replace("?","",$o->getData('title'))) . $oDelimiter . $o->getData('type') . $oDelimiter . $o->getData('is_require') . $oDelimiter . $o->getData('sort_order') . $oDelimiter . $o->getData('disabled') . $oDelimiter . $o->getData('one_time') . $oDelimiter . $o->getData('mageworx_option_image_mode') . $oDelimiter . $o->getData('exclude_first_image'). $oDelimiter . $o->getData('sku_policy') . $oDelimiter . $o->getData('group_option_id') . $oDelimiter . $o->getData('dependency_type'). $oDelimiter . $o->getData('qnty_input') . $oDelimiter . str_replace(",","{}", $o->getData('customer_groups')) . $oDelimiter . $finaloptiondescription . "," ;
							
							} else {
						
							$customoptionstitle = str_replace(" ", "_", $o->getData('title')) . "__" . $o->getData('type') . "__" . $o->getData('is_require') . "__". $o->getData('sort_order') . "," ;
							}
							
							$customoptionstitle = str_replace("?", "ce_question_mark", $customoptionstitle);
							$customoptionstitle = str_replace(":", "ce_semi_colon", $customoptionstitle);
							$customoptionstitle = str_replace("-", "ce_dash", $customoptionstitle);
							$customoptionstitle = str_replace("/", "ce_fwd_slash", $customoptionstitle);
							$customoptionstitle = str_replace("(", "ce_l_bracket", $customoptionstitle);
							$customoptionstitle = str_replace(")", "ce_r_bracket", $customoptionstitle);
							$customoptionstitle = str_replace("+", "ce_plus_sign", $customoptionstitle);
							$customoptionstitle = str_replace("&", "ce_amp_sign", $customoptionstitle);

							$CustInattrArrayAndTemp = substr_replace($customoptionstitle,"",-1);
							$attributesArray = array_merge($attributesArray, array($CustInattrArrayAndTemp => $CustInattrArrayAndTemp));
							
							if (!strpos($template, $CustInattrArrayAndTemp) !== false) {
								$template .= ''.$enclose.'{{'.$CustInattrArrayAndTemp.'}}'.$enclose.''.$delimiter.'';	
							}
							$Custdata[] = $CustInattrArrayAndTemp;
							
						}
					}
				}
			}
		}
        if($template == "" & empty($attributesArray)) {
			$content = "";
			$this->generateFile($params, $content);
		} else {
			$headers = new \Magento\Framework\DataObject($attributesArray);
			$ExtendToString = $headers->toString($template);
			$content = str_replace('__' , ':' , $ExtendToString);
			$content .= "\n";
			$storeTemplate = [];
			$cronjobarray = [];
			$cronjobarray[] = $headers->toArray();
			
			$productCollection->addAttributeToSelect(
								'name'
							  )->addAttributeToSelect(
								'price'
							  )->addAttributeToSelect(
								'special_price'
							  )->addAttributeToSelect(
								'special_from_date'
							  )->addAttributeToSelect(
								'special_to_date'
							  )->addAttributeToSelect(
								'cost'
							  )->addAttributeToSelect(
								'msrp'
							  )->addAttributeToSelect(
								'weight'
							  )->addAttributeToSelect(
								'url_key'
							  )->addAttributeToSelect(
								'meta_title'
							  )->addAttributeToSelect(
								'meta_keyword'
							  )->addAttributeToSelect(
								'meta_description'
							  )->addAttributeToSelect(
								'short_description'
							  )->addAttributeToSelect(
								'description'
							  )->addAttributeToSelect(
								'visibility'
							  )->addAttributeToSelect(
								'image'
							  )->addAttributeToSelect(
								'small_image'
							  )->addAttributeToSelect(
								'thumbnail'
							  )->addAttributeToSelect(
								'swatch_image'
							  )->addAttributeToSelect(
								'image_label'
							  )->addAttributeToSelect(
								'small_image_label'
							  )->addAttributeToSelect(
								'thumbnail_label'
							  )->addAttributeToSelect(
								'news_from_date'
							  )->addAttributeToSelect(
								'news_to_date'
							  )->addAttributeToSelect(
								'options_container'
							  )->addAttributeToSelect(
								'country_of_manufacture'
							  )->addAttributeToSelect(
								'page_layout'
							  )->addAttributeToSelect(
								'custom_design'
							  )->addAttributeToSelect('*');
								
			foreach($productCollection as $_productEntity){
				
				if($params['export_multi_store'] == "true") {
					#foreach ($this->_storeManager->getCollection()->setLoadDefault(false) as $store) {
					foreach ($this->_objectManager->create('Magento\Store\Model\Store')->getCollection()->setLoadDefault(false) as $store) {
						$_prodModel = $this->_objectManager->create('Magento\Catalog\Model\Product')->setStoreId($store->getId())->load($_productEntity->getId());
						$_product = $this->exportProductData($_prodModel, $params, $Custdata, $_stockData);
						$content .= $_product->toString($template) . "\n";	
					}
				} else {
					//$_product = $_productEntity;
					$_prodModel = $this->_objectManager->create('Magento\Catalog\Model\Product')->setStoreId(0)->load($_productEntity->getId());
					$_product = $this->exportProductData($_prodModel, $params, $Custdata, $_stockData);
					$content .= $_product->toString($template) . "\n";	
				}
				if(isset($cronjob_export_path) && !empty($cronjob_export_path) && !empty($cronjob_export_name)){
					$cronjobarray[] = $_product->toArray();
				}
				//$cron[] = $_product;
			}
			if(isset($cronjob_export_path) && !empty($cronjob_export_path) && !empty($cronjob_export_name)){
				//$cronjobarray[] = $_product->toArray();
				$this->putcsvdata($cronjobarray,$cronjob_export_path,$cronjob_export_name);
			}		
			
			if(empty($cronjob_export_path)){
				$this->generateFile($params, $content);
			}
		}
	}
	public function generateFile($params, $content)
    {
		if($params['export_manual_file_name'] != "") {
			$export_file_name = $params['export_manual_file_name'];
		} else {
			$export_file_name = "export_products.csv";
		}
		//Clean Content and replace toString has problems with these
		$content = str_replace("ce_question_mark", "?", $content);
		$content = str_replace("ce_semi_colon", "", $content);
		$content = str_replace("ce_dash", "-", $content);
		$content = str_replace("ce_fwd_slash", "/", $content);
		$content = str_replace("ce_l_bracket", "(", $content);
		$content = str_replace("ce_r_bracket", ")", $content);
		$content = str_replace("ce_plus_sign", "+", $content);
		$content = str_replace("ce_amp_sign", "&", $content);
		return $this->fileFactory->create($export_file_name, $content, DirectoryList::VAR_DIR);
	}
	public function exportProductData($_product, $params, $Custdata, $_stockData)
    {
			// THIS CLEANS HTML and other " qoute data
			foreach ($_product->getData() as $field => $_productElementData) {
				#echo "FIELD: " . $field . "<br/>";	
				#echo "VALUE: " . $_productElementData . "<br/>";
				/*
				if($field == "description") {
					$storeTemplate['description'] = $this->wrapValue($_productElementData, $params);
					continue;
				}
				*/
				if(!is_object($_productElementData) && !is_array($_productElementData) && strpos($field, '__') !== '__'){
					$storeTemplate[$field] = str_replace( '"', '""', $_productElementData);
				}
				
				$option="";
				
				if (in_array($field, $this->_systemFields) || is_object($_productElementData) || in_array($field, $this->_disabledAttributes) || $field == "category_ids") {
					continue;
				}
				$attribute = $this->getAttribute($field);
				if (!$attribute) {
					continue;
				}
				if (is_array($_productElementData)) {	
					//if a attribute is an array likely a 3rd party.. we just export that multi dimensional array into json
					$_productElementData = (string)json_encode($_productElementData);
				}
				if ($attribute->usesSource()) {
					#print_r($_productElementData);
					if (is_array($_productElementData)) {	
						if($field == "quantity_and_stock_status") { 
							$option = $attribute->getSource()->getOptionText($_productElementData['is_in_stock']);
							$_productElementData = (string)$option;
						} else {
							$option = implode(" ", $_productElementData);
							$_productElementData = (string)$option;
						}
					} else {	
						$option = $attribute->getSource()->getOptionText($_productElementData);
						if (is_array($option) && $attribute->getFrontendInput() == "multiselect") {
							$_productElementData = join(self::MULTI_DELIMITER, $option);
						} else if (is_array($option)) {
							$_productElementData = implode(self::MULTI_DELIMITER, $option);
						} else {
							$_productElementData = (string)$option;
						}
						//above is new checking for multiselect since its array and breaks it out
						#$_productElementData = (string)$option;
					}
				}
				/*
				if (is_array($option)) {
					$_productElementData = join(self::MULTI_DELIMITER, $option);
				}
				*/
				unset($option);
				if(preg_match('/"/', $_productElementData)) {
					$_productElementData = str_replace('"', '""', $_productElementData);
				}
				$storeTemplate[$field] = $_productElementData;
				
			}
			$storeTemplate['store'] = $this->storeCodeByID($_product->getStoreId());
			$storeTemplate['websites'] = $this->websiteCodeById($_product->getWebsiteIds());
			$storeTemplate['attribute_set'] = $this->attributebyid($_product->getData('attribute_set_id'));
			$storeTemplate['prodtype'] = $_product->getData('type_id');
			$storeTemplate['msrp_enabled'] = $this->msrpriceActual($_product->getData('msrp_display_actual_price_type'));
			$storeTemplate['product_id'] = $_product->getData('entity_id');
			$storeTemplate['url_path'] =  $_product->getUrlKey(); //$_product->getProductUrl();
			//Retrieve accessible external product attributes
			#$storeTemplate['msrp_display_actual_price_type'] = $this->msrpriceActual($_product->getData('msrp_display_actual_price_type'));
			$storeTemplate['store_id'] = $_product->getStoreId();
			$storeTemplate['additional_attributes'] = $this->GetExternalFields($_product->getData('entity_id'),$_product->getData('type_id'));
			
			/* PRODUCT CATEGORIES EXPORT START */
			if($params['export_category_paths'] == "true") {
				$storeTemplate['category_ids'] = $this->sptidwithcoma($_product->getCategoryIds());
				$finalimportofcategories = "";
				$okforallcategoriesnow = "";
				$finalvcategoriesproductoptions1 = "";
				$finalvcategoriesproductoptions2 = "";
				$finalvcategoriesproductoptions2before = "";
				foreach(explode(',',$storeTemplate['category_ids']) as $productcategoryId)
				{
						$cat = $this->getCategory($productcategoryId);
						#$cat = $this->_categoryModel->load($productcategoryId);
						#$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
						#$cat = $objectManager->create('Magento\Catalog\Model\Category')->load($productcategoryId);
						$finalvcategoriesproductoptions1 = $cat->getName();
						$subcatsforreverse = $cat->getParentIds();
						$subcats = array_shift($subcatsforreverse);
						$subcats1 = array_shift($subcatsforreverse);
						$finalvcategoriesproductoptions2before = "";
						foreach($subcatsforreverse as $subcatsproductcategoryId)
						{
								#$subcat = $this->_categoryModel->load($subcatsproductcategoryId);
								$subcat = $this->getCategory($subcatsproductcategoryId);
								$finalvcategoriesproductoptions2before .= $subcat->getName() . "/";
								$subsubcats = $subcat->getChildren();
						}
						$finalimportofcategories .= $finalvcategoriesproductoptions2before . 
						$finalvcategoriesproductoptions1 . " , ";
				}
				$okforallcategoriesnow = substr_replace($finalimportofcategories,"",-3);
				$storeTemplate['categories'] = $okforallcategoriesnow;
			} else {
				$storeTemplate['category_ids'] = $this->sptidwithcoma($_product->getCategoryIds());
			}
			/* PRODUCT CATEGORIES EXPORT END */
			
			/* RELATED */
			$finalrelatedproducts = "";
			$incoming_RelatedProducts = $_product->getRelatedProducts();
			foreach($incoming_RelatedProducts as $relatedproducts_str){
				if($params['export_related_position'] == "true") {
					$finalrelatedproducts .= $relatedproducts_str['position'] .":". $relatedproducts_str->getSku() . ",";
				} else {
					$finalrelatedproducts .= $relatedproducts_str->getSku() . ",";
				}
			}
			$storeTemplate['related'] = substr_replace($finalrelatedproducts,"",-1);
			/* UP SELL */
			$finalupsellproducts = "";
			$incoming_UpSellProducts = $_product->getUpSellProducts();
			foreach($incoming_UpSellProducts as $UpSellproducts_str){
				if($params['export_crossell_position'] == "true") {
					$finalupsellproducts .= $UpSellproducts_str['position'] .":". $UpSellproducts_str->getSku() . ",";
				} else {
					$finalupsellproducts .= $UpSellproducts_str->getSku() . ",";
				}
			}
			$storeTemplate['upsell'] = substr_replace($finalupsellproducts,"",-1);
			/* CROSS SELL  */
			$finalcrosssellproducts = "";
			$incoming_CrossSellProducts = $_product->getCrossSellProducts();
			foreach($incoming_CrossSellProducts as $CrossSellproducts_str){
				if($params['export_upsell_position'] == "true") {
					$finalcrosssellproducts .= $CrossSellproducts_str['position'] .":". $CrossSellproducts_str->getSku() . ",";
				} else {
					$finalcrosssellproducts .= $CrossSellproducts_str->getSku() . ",";
				}
			}
			$storeTemplate['crosssell'] = substr_replace($finalcrosssellproducts,"",-1);
			
			/* EXPORTS TIER PRICING */
			
			if(!empty($_product->getTierPrice())) {
				$tier_pricing = $this->TierPrice($_product->getTierPrice());
			} else {
				if ($attribute = $_product->getResource()->getAttribute('tier_price')) {
					$attribute->getBackend()->afterLoad($_product);
					$tier_pricing = $this->TierPrice($_product->getData('tier_price'));
				}
			}
			$storeTemplate['tier_prices'] = (string)$tier_pricing;

			/* EXPORTS ASSOICATED BUNDLE SKUS */
			if($_product->getTypeId() == "bundle") {
				$finalbundleoptions = "";
				$finalbundleselectionoptions = "";
				$finalbundleselectionoptionssorting = "";
				$optionModel = $this->_bundleOption->getResourceCollection()->setProductIdFilter($_product->getId());
				$_resource = $this->resourceConnection;
				$connection = $_resource->getConnection();
				$catalog_product_bundle_option_value = $_resource->getTableName('catalog_product_bundle_option_value');
					
				foreach($optionModel as $eachOption) {
						
						$selectOptionID = "SELECT title FROM ".$catalog_product_bundle_option_value." WHERE option_id = ".$eachOption->getData('option_id')."";
						$Optiondatarows = $connection->query($selectOptionID);
						while ($Option_row = $Optiondatarows->fetch()) {
							$finaltitle = str_replace(' ','_',$Option_row['title']);
						}
						$finalbundleoptions .=  $finaltitle . "," . $eachOption->getData('type') . "," . $eachOption->getData('required') . "," . $eachOption->getData('position') . "|";
						$selectionModel =$this->_bundleSelection->setOptionId($eachOption->getData('option_id'))->getResourceCollection();
						
						foreach($selectionModel as $eachselectionOption) {
							if($eachselectionOption->getData('option_id') == $eachOption->getData('option_id')) {
							$finalbundleselectionoptionssorting .=  $eachselectionOption->getData('sku') . ":" . $eachselectionOption->getData('selection_price_type') . ":" . $eachselectionOption->getData('selection_price_value') . ":" . $eachselectionOption->getData('is_default') . ":" . $eachselectionOption->getData('selection_qty') . ":" . $eachselectionOption->getData('selection_can_change_qty'). ":" . $eachselectionOption->getData('position') . ",";
							}
						}
						$finalbundleselectionoptionssorting = substr_replace($finalbundleselectionoptionssorting,"",-1);
						$finalbundleselectionoptionssorting .=  "|";
						$finalbundleselectionoptions = substr_replace($finalbundleselectionoptionssorting,"",-1);
				}
				$storeTemplate['bundle_options'] = substr_replace($finalbundleoptions,"",-1);
				$storeTemplate['bundle_selections'] = substr_replace($finalbundleselectionoptions,"",-1);
			}
			
			/* EXPORTS DOWNLOADABLE OPTIONS */
			$finaldownloabledproductoptions = "";
			$finaldownloabledsampleproductoptions = "";
			
			if($_product->getTypeId() == "downloadable") {
			$_linkCollection = $this->_downloadableLink->getCollection()
								->addProductToFilter($_product->getId())
								->addTitleToResult($_product->getStoreId())
								->addPriceToResult($_product->getStore()->getWebsiteId());

			 foreach ($_linkCollection as $link) {
			 
				if($link->getLinkType() =="url" && $link->getSampleType() =="url") {
				$finaldownloabledproductoptions .= $link->getTitle() . "," . $link->getPrice() . "," . $link->getNumberOfDownloads() . "," . $link->getLinkType() . "," . $link->getLinkUrl() . ",url," . $link->getSampleUrl() . "|";
				} else if($link->getLinkType() =="url" && $link->getSampleType() =="file") {
				$finaldownloabledproductoptions .= $link->getTitle() . "," . $link->getPrice() . "," . $link->getNumberOfDownloads() . "," . $link->getLinkType() . "," . $link->getLinkUrl() . "," . $link->getSampleType() . "," . $link->getSampleFile() . "|";
				} else if($link->getLinkType() =="file" && $link->getSampleType() =="url") {
				$finaldownloabledproductoptions .= $link->getTitle() . "," . $link->getPrice() . "," . $link->getNumberOfDownloads() . "," . $link->getLinkType() . "," . $link->getLinkFile() . "," . $link->getSampleType() . "," . $link->getSampleUrl() . "|";
				} else if($link->getLinkType() =="file" && $link->getSampleType() =="file") {
				$finaldownloabledproductoptions .= $link->getTitle() . "," . $link->getPrice() . "," . $link->getNumberOfDownloads() . "," . $link->getLinkType() . "," . $link->getLinkFile() . ",file," . $link->getSampleFile() . "|";
				}else if($link->getLinkType() =="file" && $link->getSampleType() ==""){
				$finaldownloabledproductoptions .= $link->getTitle() . "," . $link->getPrice() . "," . $link->getNumberOfDownloads() . "," . $link->getLinkType() . "," . $link->getLinkFile() . ",file," . $link->getSampleFile() . "|";
				}else if($link->getLinkType() =="url" && $link->getSampleType() ==""){
				$finaldownloabledproductoptions .= $link->getTitle() . "," . $link->getPrice() . "," . $link->getNumberOfDownloads() . "," . $link->getLinkType() . "," . $link->getLinkUrl() . ",url," . $link->getSampleUrl() . "|";
				}else if($link->getLinkType() =="" && $link->getSampleType() =="file"){
				$finaldownloabledproductoptions .= $link->getTitle() . "," . $link->getPrice() . "," . $link->getNumberOfDownloads() . "," . $link->getLinkType() . "," . $link->getLinkFile() . "," . $link->getSampleType(). "," . $link->getSampleFile() . "|";
				}else if($link->getLinkType() =="" && $link->getSampleType() =="url"){
				$finaldownloabledproductoptions .= $link->getTitle() . "," . $link->getPrice() . "," . $link->getNumberOfDownloads() . "," . $link->getLinkType() . "," . $link->getLinkUrl() . "," . $link->getSampleType() . "," . $link->getSampleUrl() . "|";
				}else{
					$finaldownloabledproductoptions .= "";
				}
				
				
			 }
			 	$storeTemplate['downloadable_options'] = substr_replace($finaldownloabledproductoptions,"",-1);
				$_linkSampleCollection = $this->_downloadableSample->getCollection()
									->addProductToFilter($_product->getId())
									->addTitleToResult($_product->getStoreId());
			
				foreach ($_linkSampleCollection as $sample_link) {
					/* @var Mage_Downloadable_Model_Sample $sample_link */
					#Main file,file,/test.mp3,/sample.mp3
					if($sample_link->getSampleType() == "url") {
						$finaldownloabledsampleproductoptions .= $sample_link->getTitle() . "," . $sample_link->getSampleType() . "," . $sample_link->getSampleUrl() . "|";
					} else if($sample_link->getSampleType() == "file") {
						$finaldownloabledsampleproductoptions .= $sample_link->getTitle() . "," . $sample_link->getSampleType() . "," . $sample_link->getSampleFile() . "|";
					}
				}
			 $storeTemplate['downloadable_sample_options'] = substr_replace($finaldownloabledsampleproductoptions,"",-1);
			 
			} else {
					$storeTemplate['downloadable_sample_options'] = "";
					$storeTemplate['downloadable_options'] = "";
			}// end for check of downloadable type
			
						
			/* EXPORTS ASSOICATED CONFIGURABLE SKUS */
			$storeTemplate['associated'] = '';
			if($_product->getTypeId() == "configurable") {
				$associatedProducts = $_product->getTypeInstance()->getUsedProducts($_product, null);
				foreach($associatedProducts as $associatedProduct) {
						$storeTemplate['associated'] .= $associatedProduct->getSku() . ",";
						#$storeTemplate['associated'] .= $associatedProduct->getSku() . "|";
				}
			}
			/* EXPORTS ASSOICATED GROUPED SKUS */
			$storeTemplate['grouped'] = '';
			if($_product->getTypeId() == "grouped") {
				$associatedProducts = $_product->getTypeInstance()->getAssociatedProducts($_product, null);
				foreach($associatedProducts as $associatedProduct) {
						if($params['export_grouped_position'] == "true") {
							#$storeTemplate['grouped'] .= $associatedProduct->getPosition() . ":" . $associatedProduct->getSku() . ":" . $associatedProduct->getQty() . ",";
							$storeTemplate['grouped'] .= $associatedProduct->getSku() . ":" . $associatedProduct->getQty() . ":" . $associatedProduct->getPosition(). ",";
						} else {
							$storeTemplate['grouped'] .= $associatedProduct->getSku() . ",";
						}
				}
			}
			/* IMAGE EXPORT [START] */
			
			if($params['export_full_image_paths'] == "true") {
				$getBaseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
				if($_product->getData('image')!="") { $storeTemplate['image'] = $getBaseUrl . "catalog/product" . $_product->getData('image'); }
				if($_product->getData('small_image')!="") { $storeTemplate['small_image'] = $getBaseUrl . "catalog/product" . $_product->getData('small_image'); }
				if($_product->getData('thumbnail')!="") { $storeTemplate['thumbnail'] = $getBaseUrl . "catalog/product" . $_product->getData('thumbnail'); }
				if($_product->getData('swatch_image')!="") { $storeTemplate['swatch_image'] = $getBaseUrl . "catalog/product" . $_product->getData('swatch_image'); }
			}
			
			/* IMAGE EXPORT [END] */
			
			/* GALLERY IMAGE EXPORT [START] */
			$finalgalleryimages = "";
			$galleryImagesModel = $this->getMediaGallery(array($_product->getId()));
		
			if (count($galleryImagesModel) > 0) {
				#$getBaseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
				foreach ($galleryImagesModel as $_image) {
					#if($params['export_full_image_paths'] == "true") {
					if(!in_array($_image['_media_image'], array($_product->getData('image'),$_product->getData('small_image'),$_product->getData('thumbnail')), true )) {
						if ($params['export_full_image_paths'] == "true") {
							$finalgalleryimages .= $getBaseUrl . "catalog/product" .  $_image['_media_image'] . ",";
						} else {
							$finalgalleryimages .= $_image['_media_image'] . ",";
						}
					}
				}
			}
			
			$storeTemplate['gallery'] = substr_replace($finalgalleryimages,"",-1);
			/* GALLERY IMAGE EXPORT [END] */
			
			/* ADDITIONAL IMAGE LABEL EXPORT FOR 1.4 ONLY [START] */
			$finalgallerylabelimages = "";
			if (count($galleryImagesModel) > 0) {
				foreach ($galleryImagesModel as $_image) {
					if($_image['_media_label'] !="") { $finalgallerylabelimages .= $_image['_media_label'] . ";"; }
				}
			}
			$storeTemplate['gallery_label'] = substr_replace($finalgallerylabelimages,"",-1);
			
			/* ADDITIONAL IMAGE LABEL EXPORT FOR 1.4 ONLY [END] */
			
			/* EXPORTS CONFIGURABLE ATTRIBUTES [START] */
			$storeTemplate['config_attributes'] = '';
			$finalproductattributes = "";
			//check if product is a configurable type or not
			if($_product->getTypeId() == "configurable") {
				 //get the configurable data from the product
				 $config = $_product->getTypeInstance(true);
				 //loop through the attributes                                  
				 foreach($config->getConfigurableAttributesAsArray($_product) as $attributes)
				 {
					$finalproductattributes .= $attributes['attribute_code'] . ",";
					#$finalproductattributes .= $attributes['attribute_code'] . "|";
				 }
			}
			$storeTemplate['config_attributes'] = substr_replace($finalproductattributes,"",-1);
			/* EXPORTS CONFIGURABLE ATTRIBUTES [END] */
			
			/*  EXPORT PRODUCT OPTIONS [START]  */
			
			if ($this->moduleManager->isEnabled('MageWorx_OptionBase')) {
						
				$storeTemplate['enable_absolute_price'] = $_product->getData('absolute_price');
				$storeTemplate['enable_sku_policy'] = $_product->getData('sku_policy');
				/*  EMPTY CUSTOM OPTION TITLE  */
				foreach ($Custdata as $OptTitl){
					$storeTemplate[$OptTitl] = "";
				}
				if(is_array($_product->getOptions())) {
					foreach ($_product->getOptions() as $CustmOptData) {
						
						$customoptionvalues = "";
						$customoptionitemvaluecounter = 0;
						$finaloptiondescription = $CustmOptData->getData('description');
						//defaults
						$oDelimiter   		= "__"; //"[]"
						$price_type			= "fixed"; 
						$price				= "0.0000"; 
						$sku 				= " ";
						$special_price		= "0.00";
						$special_comment	= " ";
						$sort_order 		= "0";
						$customoptions_qty  = "0";
						$max_characters		= "0";
						$imagesdata			= array();
						$in_group_id		= "0";
						$tier_pricing 		= ""; 
						$dependent_ids		= "";
						$image_path			= "";
						$image_color		= "";
						//defaults
						
						//m1 to m2 mapping
						//view_mode = disabled	
						//is_dependent = dependency_type
						//in_group_id = group_option_id
						//customoptions_is_onetime = one_time
						//image_mode = mageworx_option_image_mode
						//customer_groups = gone in m2??
						
						$CustOptnTitle = str_replace(" ", "_", str_replace("?","",$CustmOptData->getData('title'))) . $oDelimiter . $CustmOptData->getData('type') . $oDelimiter . $CustmOptData->getData('is_require') . $oDelimiter . $CustmOptData->getData('sort_order') . $oDelimiter . $CustmOptData->getData('disabled') . $oDelimiter . $CustmOptData->getData('one_time') . $oDelimiter . $CustmOptData->getData('mageworx_option_image_mode') . $oDelimiter . $CustmOptData->getData('exclude_first_image'). $oDelimiter . $CustmOptData->getData('sku_policy') . $oDelimiter . $CustmOptData->getData('group_option_id') . $oDelimiter . $CustmOptData->getData('dependency_type'). $oDelimiter . $CustmOptData->getData('qnty_input') . $oDelimiter . str_replace(",","{}", $CustmOptData->getData('customer_groups')) . $oDelimiter . $finaloptiondescription;
						
						$CustOptnTitle = str_replace("?", "ce_question_mark", $CustOptnTitle);
						$CustOptnTitle = str_replace(":", "ce_semi_colon", $CustOptnTitle);
						$CustOptnTitle = str_replace("-", "ce_dash", $CustOptnTitle);
						$CustOptnTitle = str_replace("/", "ce_fwd_slash", $CustOptnTitle);
						$CustOptnTitle = str_replace("(", "ce_l_bracket", $CustOptnTitle);
						$CustOptnTitle = str_replace(")", "ce_r_bracket", $CustOptnTitle);
						$CustOptnTitle = str_replace("+", "ce_plus_sign", $CustOptnTitle);
						$CustOptnTitle = str_replace("&", "ce_amp_sign", $CustOptnTitle);
					
						if($CustmOptData->getData('type')=="swatch" || $CustmOptData->getData('type')=="multiswatch" || $CustmOptData->getData('type')=="checkbox" || $CustmOptData->getData('type')=="drop_down" || $CustmOptData->getData('type')=="radio" || $CustmOptData->getData('type')=="multiple") {
							if(is_array($CustmOptData->getValues())) {
								foreach ( $CustmOptData->getValues() as $oValues ) {
									#print_r($oValues->getData());
									//m1 to m2 mappings
									//in_group_id = group_option_value_id
									//tiers = tier_price
									//images = images_data
									if($oValues->getData('price_type')=="") { $price_type = "fixed"; } else { $price_type = $oValues->getData('price_type'); }
									if($oValues->getData('price')=="") { $price = "0.0000"; } else { $price = $oValues->getData('price'); }
									if($oValues->getData('sku')=="") { $sku = " "; } else { $sku = $oValues->getData('sku'); }
								    if($oValues->getData('special_price')!="") {  $special_price = $oValues->getData('special_price'); }
								    if($oValues->getData('special_comment')!="") { $special_comment = $oValues->getData('special_comment'); }
									if($oValues->getData('sort_order')=="") { $sort_order = "0"; } else { $sort_order = $oValues->getData('sort_order'); }
								    if($oValues->getData('qty')!="") { $customoptions_qty = $oValues->getData('qty'); }
									if($oValues->getData('max_characters')=="") { $max_characters = "0"; } else { $max_characters = $oValues->getData('max_characters'); }
								    if($oValues->getData('images_data')!="") { $imagesdata = $oValues->getData('images_data');  }
								    if($oValues->getData('group_option_value_id')!="") { $in_group_id = $oValues->getData('group_option_value_id'); }
								    if($oValues->getData('tier_price')!="") { $tier_pricing = $oValues->getData('tier_price'); }
								    if($oValues->getData('dependent_ids')!="") { $dependent_ids = $oValues->getData('dependent_ids'); }
									
									if(!empty($imagesdata)) {
										if($this->getVersion() > '2.2.0') {
											$jsonSerializer = $this->_objectManager->get('Magento\Framework\Serialize\Serializer\Json');
											$imagesdataArr = $jsonSerializer->unserialize($imagesdata);
										} else {
											$imagesdataArr = unserialize($imagesdata);
										}
										if($imagesdataArr[$customoptionitemvaluecounter]['custom_media_type'] == "image") {
											$image_path = $imagesdataArr[$customoptionitemvaluecounter]['value'];
										}
										if($imagesdataArr[$customoptionitemvaluecounter]['custom_media_type'] =="color") {
											$image_color = $imagesdataArr[$customoptionitemvaluecounter]['color'];
										}
									}
									
									$customoptionvalues .= $oValues->getData('title').":".$price_type.":".$price.":".$sku.":".$sort_order.":".$max_characters.":".$customoptions_qty.":".$special_price.":".$special_comment.":".$image_path.":".$image_color.":".$in_group_id; 
									
									if($tier_pricing != "") {   
											$customoptionvalues .= ":";  
											$buildcustomoptionvalues = "";              
											foreach ($tier_pricing as $eachtierPrice) {
												$buildcustomoptionvalues .= $eachtierPrice['customer_group_id'] . "=" . $eachtierPrice['qty']. "=" . $eachtierPrice['price']. "=" . $eachtierPrice['price_type'] . "~";
											} 
											$customoptionvalues .= substr_replace($buildcustomoptionvalues,"",-1);
									}
									if($dependent_ids != "") {
											$dependentIdsconvert = array();
											$dependentIdsTmp = explode(',', $dependent_ids);                        
											foreach ($dependentIdsTmp as $d_id) {
												$dependentIdsconvert[] = $this->getViewIGI($d_id);
											}
											$dependentIdsview = implode(',', $dependentIdsconvert);
											$customoptionvalues .= "^" . $dependentIdsview;
									} 
									$customoptionvalues .= "|";
								}
							} else {
								//lets log that this product ID has a custom option that contains no values and it should based on its type
								//echo "THIS SKU HAS BLANK CUSTOM OPTION VALUE: " . $_product->getSku();
							}
							
						  } else {
							
							//m1 to m2 mappings
							//in_group_id = group_option_value_id
							//tiers = tier_price
							//images = images_data
							//customoptions_qty = qty
							
							if($CustmOptData->getData('price_type')=="") { $price_type = "fixed"; } else { $price_type = $CustmOptData->getData('price_type'); }
							if($CustmOptData->getData('price')=="") { $price = "0.0000"; } else { $price = $CustmOptData->getData('price'); }
							if($CustmOptData->getData('sku')=="") { $sku = " "; } else { $sku = $CustmOptData->getData('sku'); }
							if($CustmOptData->getData('special_price')!="") { $special_price = $CustmOptData->getData('special_price'); }
							if($CustmOptData->getData('special_comment')!="") { $special_comment = $CustmOptData->getData('special_comment'); }
							if($CustmOptData->getData('sort_order')=="") { $sort_order = "0"; } else { $sort_order = $CustmOptData->getData('sort_order'); }
							if($CustmOptData->getData('qty')!="") { $customoptions_qty = $CustmOptData->getData('qty'); }
							if($CustmOptData->getData('max_characters')=="") { $max_characters = "0"; } else { $max_characters = $CustmOptData->getData('max_characters'); }
							if($CustmOptData->getData('images_data')!="") { $imagesdata = $CustmOptData->getData('images_data');  }
							if($CustmOptData->getData('group_option_value_id')!="") { $in_group_id = $CustmOptData->getData('group_option_value_id'); }
							if($CustmOptData->getData('tier_price')!="") { $tier_pricing = $CustmOptData->getData('tier_price'); }
							if($CustmOptData->getData('dependent_ids')!="") { $dependent_ids = $CustmOptData->getData('dependent_ids'); }
							if(!empty($imagesdata)) {
								if($this->getVersion() > '2.2.0') {
									$jsonSerializer = $this->_objectManager->get('Magento\Framework\Serialize\Serializer\Json');
									$imagesdataArr = $jsonSerializer->unserialize($imagesdata);
								} else {
									$imagesdataArr = unserialize($imagesdata);
								}
								if($imagesdataArr[$customoptionitemvaluecounter]['custom_media_type'] == "image") {
									$image_path = $imagesdataArr[$customoptionitemvaluecounter]['value'];
								}
								if($imagesdataArr[$customoptionitemvaluecounter]['custom_media_type'] =="color") {
									$image_color = $imagesdataArr[$customoptionitemvaluecounter]['color'];
								}
							}
							$customoptionvalues .= $CustmOptData->getData('title').":".$price_type.":".$price.":".$sku.":".$sort_order.":".$max_characters.":".$customoptions_qty.":".$special_price.":".$special_comment.":".$image_path.":".$image_color.":".$in_group_id; 
							
							if($tier_pricing != "") {                   
									foreach ($tier_pricing as $eachtierPrice) {
										$customoptionvalues .= ":" . $eachtierPrice['customer_group_id'] . "=" . $eachtierPrice['qty']. "=" . $eachtierPrice['price']. "=" . $eachtierPrice['price_type'];
									}
							}
							if($dependent_ids != "") {
								$dependentIdsconvert = array();
								$dependentIdsTmp = explode(',', $dependent_ids);                        
								foreach ($dependentIdsTmp as $d_id) {
									$dependentIdsconvert[] = $this->getViewIGI($d_id);
								}
								$dependentIdsview = implode(',', $dependentIdsconvert);
								$customoptionvalues .= "^" . $dependentIdsview;
							} 
							$customoptionvalues .= "|";
						  }
						  $storeTemplate[$CustOptnTitle] = substr_replace($customoptionvalues,"",-1);
						  $customoptionitemvaluecounter++;
					}
				}
			} else {
				/*  normal export non mageworx  */
				/*  EMPTY CUSTOM OPTION TITLE  */
				foreach ($Custdata as $OptTitl){
					$storeTemplate[$OptTitl] = "";
				}
				if(is_array($_product->getOptions())) {
					foreach ($_product->getOptions() as $CustmOptData) {
						
						$customoptionvalues = "";
						$CustOptnTitle = str_replace(" ", "_", $CustmOptData->getData('title')) . "__" . $CustmOptData->getData('type') . "__" . $CustmOptData->getData('is_require') . "__". $CustmOptData->getData('sort_order');	
						$CustOptnTitle = str_replace("?", "ce_question_mark", $CustOptnTitle);
						$CustOptnTitle = str_replace(":", "ce_semi_colon", $CustOptnTitle);
						$CustOptnTitle = str_replace("-", "ce_dash", $CustOptnTitle);
						$CustOptnTitle = str_replace("/", "ce_fwd_slash", $CustOptnTitle);
						$CustOptnTitle = str_replace("(", "ce_l_bracket", $CustOptnTitle);
						$CustOptnTitle = str_replace(")", "ce_r_bracket", $CustOptnTitle);
						$CustOptnTitle = str_replace("+", "ce_plus_sign", $CustOptnTitle);
						$CustOptnTitle = str_replace("&", "ce_amp_sign", $CustOptnTitle);
						if($CustmOptData->getData('type')=="checkbox" || $CustmOptData->getData('type')=="drop_down" || $CustmOptData->getData('type')=="radio" || $CustmOptData->getData('type')=="multiple") {
							if(is_array($CustmOptData->getValues())) {
								foreach ( $CustmOptData->getValues() as $oValues ) {
									if($oValues->getData('price_type')=="") { $price_type = "fixed"; } else { $price_type = $oValues->getData('price_type'); }
									if($oValues->getData('price')=="") { $price = "0.0000"; } else { $price = $oValues->getData('price'); }
									if($oValues->getData('sku')=="") { $sku = " "; } else { $sku = $oValues->getData('sku'); }
									if($oValues->getData('sort_order')=="") { $sort_order = "0"; } else { $sort_order = $oValues->getData('sort_order'); }
									if($oValues->getData('max_characters')=="") { $max_characters = "0"; } else { $max_characters = $oValues->getData('max_characters'); }
									$customoptionvalues .= $oValues->getData('title') . ":" . $price_type . ":" . $price . ":" . $sku . ":" . $sort_order . ":" . $max_characters . "|";
								}
							} else {
								//lets log that this product ID has a custom option that contains no values and it should based on its type
							}
							
						  }else{
							if($CustmOptData->getData('price_type')=="") { $price_type = "fixed"; } else { $price_type = $CustmOptData->getData('price_type'); }
							if($CustmOptData->getData('price')=="") { $price = "0.0000"; } else { $price = $CustmOptData->getData('price'); }
							if($CustmOptData->getData('sku')=="") { $sku = " "; } else { $sku = $CustmOptData->getData('sku'); }
							if($CustmOptData->getData('sort_order')=="") { $sort_order = "0"; } else { $sort_order = $CustmOptData->getData('sort_order'); }
							if($CustmOptData->getData('max_characters')=="") { $max_characters = "0"; } else { $max_characters = $CustmOptData->getData('max_characters'); }
							$customoptionvalues .= $CustmOptData->getData('title') . ":" . $price_type . ":" . $price . ":" . $sku . ":" . $sort_order . ":" . $max_characters . "|";
							
						  }
						  $storeTemplate[$CustOptnTitle] = substr_replace($customoptionvalues,"",-1);
					}
				}
			}
			
			/*  EXPORT PRODUCT OPTIONS [END]  */
			$storeTemplate['qty'] = $_stockData->getStockItem($_product->getData('entity_id'))->getQty();
			$storeTemplate['min_qty'] = $_stockData->getStockItem($_product->getData('entity_id'))->getMinQty();
			$storeTemplate['use_config_min_qty'] = $_stockData->getStockItem($_product->getData('entity_id'))->getUseConfigMinQty();
			$storeTemplate['is_qty_decimal'] = $_stockData->getStockItem($_product->getData('entity_id'))->getData('is_qty_decimal');
			$storeTemplate['backorders'] = $_stockData->getStockItem($_product->getData('entity_id'))->getData('backorders');
			$storeTemplate['use_config_backorders'] = $_stockData->getStockItem($_product->getData('entity_id'))->getData('use_config_backorders');
			$storeTemplate['min_sale_qty'] = $_stockData->getStockItem($_product->getData('entity_id'))->getMinSaleQty();
			$storeTemplate['use_config_min_sale_qty'] = $_stockData->getStockItem($_product->getData('entity_id'))->getUseConfigMinSaleQty();
			$storeTemplate['max_sale_qty'] = $_stockData->getStockItem($_product->getData('entity_id'))->getMaxSaleQty();
			$storeTemplate['use_config_max_sale_qty'] = $_stockData->getStockItem($_product->getData('entity_id'))->getUseConfigMaxSaleQty();
			$storeTemplate['is_in_stock'] = $_stockData->getStockItem($_product->getData('entity_id'))->getIsInStock();
			$storeTemplate['low_stock_date'] = $_stockData->getStockItem($_product->getData('entity_id'))->getData('low_stock_date');
			$storeTemplate['notify_stock_qty'] = $_stockData->getStockItem($_product->getData('entity_id'))->getNotifyStockQty();
			$storeTemplate['use_config_notify_stock_qty'] = $_stockData->getStockItem($_product->getData('entity_id'))->getUseConfigNotifyStockQty();
			$storeTemplate['manage_stock'] = $_stockData->getStockItem($_product->getData('entity_id'))->getManageStock();
			$storeTemplate['use_config_manage_stock'] = $_stockData->getStockItem($_product->getData('entity_id'))->getUseConfigManageStock();
			$storeTemplate['stock_status_changed_auto'] = $_stockData->getStockItem($_product->getData('entity_id'))->getData('stock_status_changed_auto');
			$storeTemplate['use_config_qty_increments'] = $_stockData->getStockItem($_product->getData('entity_id'))->getData('use_config_qty_increments');
			$storeTemplate['qty_increments'] = $_stockData->getStockItem($_product->getData('entity_id'))->getData('qty_increments');
			$storeTemplate['enable_qty_increments'] = $_stockData->getStockItem($_product->getData('entity_id'))->getData('enable_qty_increments');
			$storeTemplate['is_decimal_divided'] = $_stockData->getStockItem($_product->getData('entity_id'))->getData('is_decimal_divided');
			$storeTemplate['use_config_enable_qty_increments'] = $_stockData->getStockItem($_product->getData('entity_id'))->getData('use_config_enable_qty_inc');
			$storeTemplate['use_config_enable_qty_inc'] = $_stockData->getStockItem($_product->getData('entity_id'))->getData('use_config_enable_qty_inc');
			$storeTemplate['stock_status_changed_automatically'] = $_stockData->getStockItem($_product->getData('entity_id'))->getData('stock_status_changed_auto');
			
			#print_r($storeTemplate);
			#exit;
			$_product->addData($storeTemplate);

			unset($storeTemplate);
			
			return $_product;
	
	}
	public function getViewIGI($IGI) {        
        return (($IGI<65536)?$IGI:floor($IGI/65535).'x'.$IGI%65535);
    }
	public function putcsvdata($data,$cronjob_export_path, $cronjob_export_name){
		//$name = "CronJob_exported_products.csv";
		//$this->fileFactory->create('export_products.csv', $content, DirectoryList::VAR_DIR);
		//Warning: fopen(var/ProductImportExport/cron_export_products.csv): failed to open stream: No such file or directory in /home/scottb61/public_html/app/code/CommerceExtensions/ProductImportExport/Controller/Adminhtml/Data/ExportPost.php on line 877
		//$importDir = $this->getMediaDirImportDir();
		//$url = $importDir . '/' . $cronjob_export_path.'/'.$cronjob_export_name;
		#$fullPath = getcwd();
		$fileDirectoryPath = $this->directoryList->getPath(DirectoryList::VAR_DIR);
		
		if(!is_dir($fileDirectoryPath))
			mkdir($fileDirectoryPath, 0777, true);
		$filePath =  $fileDirectoryPath .'/'. $cronjob_export_path .'/'. $cronjob_export_name;
	
		#$data2 = [];
		/* pass data array to write in csv file */
		#$data2 = [['column 1','column 2','column 3'],['100001','test','test2']];
		
		#$csvProcessor
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$csvProcessor = $objectManager->get('Magento\Framework\File\Csv');
		$csvProcessor
			->setEnclosure('"')
			->setDelimiter(',')
			->saveData($filePath, $data);
		/*
		$fullPath = getcwd();	
		$url = $fullPath.'/'.$cronjob_export_path.'/'.$cronjob_export_name;
		$outstream = fopen($url, 'w');	 
			$first = true;
			$temp = $data['0'];    
			foreach($data as $result){
				if($first){
					$titles = array();
					foreach($temp as $key=>$val){
						$titles[] = $key;
					}
				//print_r ($titles);exit;
					fputcsv($outstream, $titles);
				}
				$first = false;
				fputcsv($outstream, $result);
			} 
			fclose($outstream); 
		*/
	}
	
    private function wrapValue($value, $params)
    {
		#$pos = strpos($params['export_delimiter'], '"');
		#if ($pos !== false) {
        #if ($params['export_delimiter'] == "\"") {
			/*
            $wrap = function ($value) {
                return sprintf('"%s"', str_replace('"', '""', $value));
            };
            $value = is_array($value) ? array_map($wrap, $value) : $wrap($value);
			*/
			$value = str_replace('"', '""', $value);
			$value = str_replace("\r\n", "\n", $value); // windows -> unix
			$value = str_replace("\r", "\n", $value);   // remaining -> unix
			$value = str_replace (array("\r\n", "\n", "\r"), ' ', $value);
			$value = preg_replace('/\R/', '', $value); // remove new line symbols of all types (in utf8)
			$value = preg_replace('/\s/', '', $value); //Remove all whitespace characters
        #}
        return $value;
    }
	
	public function GetExternalFields($ProductId,$ProductType){
		if($ProductType == 'configurable' && $ProductId !=""){
			$product = $this->_productModel->load($ProductId); 
			$config = $product->getTypeInstance(true);
			$ProductData = array();
			foreach($config->getConfigurableAttributesAsArray($product) as $attributes)
			{
				try {
					$associated_products = $this->_configurableProduct->getUsedProductCollection($product)->addAttributeToSelect('*')->addFilterByRequiredOptions();
				} catch (\Exception $e) {
					throw new \Magento\Framework\Exception\LocalizedException(__("BAD CONFIGURABLE PRODUCT CHECK ID (and fix/remove): " . $ProductId), $e);
				}
				foreach($associated_products as $Associatedproduct){
					$ProductData[]= $Associatedproduct->getSku() .'='.$attributes['attribute_code'] .'='. $Associatedproduct->getAttributeText($attributes['attribute_code']);	
				}
			}
			return implode(',',$ProductData);
		}
	}
	
    public function getAttribute($code)
    {
        if (!isset($this->_attributes[$code])) {
            $this->_attributes[$code] = $this->_productModel->getResource()->getAttribute($code);
        }
        return $this->_attributes[$code];
    }
	
	public function sptidwithcoma($sdata){
		$data =	implode(",",$sdata);
		return $data;
	}
	
	public function storeCodeByID($storeid){
		return $this->_storeManager->getStore($storeid)->getCode();
	}
	
	public function msrpriceActual($msrp_displayActualPrice){
		$data ="";
		if($msrp_displayActualPrice == 0){ $data .= "Use config"; }
		if($msrp_displayActualPrice == 1){ $data .= "On Gesture"; }
		if($msrp_displayActualPrice == 2){ $data .= "In Cart"; }
		if($msrp_displayActualPrice == 3){ $data .= "Before Order Confirmation"; }
		return $data;
	}
	
	public function TierPrice($incoming_tierps){
		$data="";
		if(is_array($incoming_tierps)) {
			$export_data="";
			foreach($incoming_tierps as $tier_str){
				#print_r($tier_str);
				if(isset($tier_str['value_type'])) {
					$export_data .= $tier_str['cust_group'] . "=" . round($tier_str['price_qty']) . "=" . $tier_str['price'] . "=" . $tier_str['value_type'] . "|";
				} else {
					$export_data .= $tier_str['cust_group'] . "=" . round($tier_str['price_qty']) . "=" . $tier_str['price'] . "|";
				}
			}
			$data = substr_replace($export_data,"",-1);
		}
		
		return $data;
	}
	
	public function websiteCodeById($webid){
		$withname = array();
		foreach($webid as $webids){
			$withname[] = $this->_storeManager->getWebsite($webids)->getCode();
		}
		$data =	implode(",",$withname);
		return $data;
	}
	
	public function attributebyid($attributeid){
		$data = $this->_attributeSet->load($attributeid)->getAttributeSetName();
		return $data;
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
