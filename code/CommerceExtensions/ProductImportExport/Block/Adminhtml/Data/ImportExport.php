<?php
/**
 * Copyright © 2019 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CommerceExtensions\ProductImportExport\Block\Adminhtml\Data;

class ImportExport extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_template = 'importExport.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\App\ResourceConnection $resource,
		\Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $productAttributeCollection,
		\Magento\Catalog\Model\Product\AttributeSet\Options $productAttributeSets,
		\Magento\Framework\Serialize\Serializer\Json $unserializer,
		\CommerceExtensions\ProductImportExport\Model\Data\CsvImportHandler $csvImportHandler,
		array $data = []
	) {
        parent::__construct($context, $data);
        $this->resource = $resource;
        $this->productAttributeCollection = $productAttributeCollection;
        $this->productAttributeSets = $productAttributeSets;
        $this->unserializer = $unserializer;
        $this->csvImportHandler = $csvImportHandler;
        $this->setUseContainer(true);
    }
	
	public function getImportIsRunning()
    {
		$importStatus = $this->csvImportHandler->_getNotCachedRow('importstatus', 0);
		if($importStatus->getValue() != "" && $importStatus->getValue() != "canceled" && $importStatus->getValue() != "finished") {
			return true;
		} else {
			return false;
		}
	}
	
    public function getSerializedValue($value)
    {
        if (empty($value)) return false;
		$ob = json_decode($value);
		if($ob === null) {
		    // $ob is null because the json cannot be decoded
			return false;
		}
        return $this->unserializer->unserialize($value);
    }
	
    public function getMappedFields($mappedFields)
    {
		return $this->getSerializedValue($mappedFields);
    }
	/**
     * Retrieve Product Attributes
     *
     * @return html
     * @codeCoverageIgnore
     */
    public function getProductAttributes($seletedData="")
    {
		$productAttributes = $this->productAttributeCollection->load();
		$additionlProductAttributes = '<option value="0">Choose an attribute</option>';
			if($seletedData == "associated") {
				$additionlProductAttributes .= '<option selected value="associated">associated</option>';
			} else {
				$additionlProductAttributes .= '<option value="associated">associated</option>';
			}
			if($seletedData == "attribute_set") {
				$additionlProductAttributes .= '<option selected value="attribute_set">attribute_set</option>';
			} else {
				$additionlProductAttributes .= '<option value="attribute_set">attribute_set</option>';
			}
			if($seletedData == "categories") {
				$additionlProductAttributes .= '<option selected value="categories">categories</option>';
			} else {
				$additionlProductAttributes .= '<option value="categories">categories</option>';
			}
			if($seletedData == "config_attributes") {
				$additionlProductAttributes .= '<option selected value="config_attributes">config_attributes</option>';
			} else {
				$additionlProductAttributes .= '<option value="config_attributes">config_attributes</option>';
			}
			if($seletedData == "downloadable_options") {
				$additionlProductAttributes .= '<option selected value="downloadable_options">downloadable_options</option>';
			} else {
				$additionlProductAttributes .= '<option value="downloadable_options">downloadable_options</option>';
			}
			if($seletedData == "downloadable_sample_options") {
				$additionlProductAttributes .= '<option selected value="downloadable_sample_options">downloadable_sample_options</option>';
			} else {
				$additionlProductAttributes .= '<option value="downloadable_sample_options">downloadable_sample_options</option>';
			}
			if($seletedData == "gallery") {
				$additionlProductAttributes .= '<option selected value="gallery">gallery</option>';
			} else {
				$additionlProductAttributes .= '<option value="gallery">gallery</option>';
			}
			if($seletedData == "gallery_label") {
				$additionlProductAttributes .= '<option selected value="gallery_label">gallery_label</option>';
			} else {
				$additionlProductAttributes .= '<option value="gallery_label">gallery_label</option>';
			}
			if($seletedData == "grouped") {
				$additionlProductAttributes .= '<option selected value="grouped">grouped</option>';
			} else {
				$additionlProductAttributes .= '<option value="grouped">grouped</option>';
			}
			if($seletedData == "is_in_stock") {
				$additionlProductAttributes .= '<option selected value="is_in_stock">is_in_stock</option>';
			} else {
				$additionlProductAttributes .= '<option value="is_in_stock">is_in_stock</option>';
			}
			if($seletedData == "min_qty") {
				$additionlProductAttributes .= '<option selected value="min_qty">min_qty</option>';
			} else {
				$additionlProductAttributes .= '<option value="min_qty">min_qty</option>';
			}
			if($seletedData == "qty") {
				$additionlProductAttributes .= '<option selected value="qty">qty</option>';
			} else {
				$additionlProductAttributes .= '<option value="qty">qty</option>';
			}
			if($seletedData == "qty_increments") {
				$additionlProductAttributes .= '<option selected value="qty_increments">qty_increments</option>';
			} else {
				$additionlProductAttributes .= '<option value="qty_increments">qty_increments</option>';
			}
			if($seletedData == "enable_qty_increments") {
				$additionlProductAttributes .= '<option selected value="enable_qty_increments">enable_qty_increments</option>';
			} else {
				$additionlProductAttributes .= '<option value="enable_qty_increments">enable_qty_increments</option>';
			}
			if($seletedData == "related") {
				$additionlProductAttributes .= '<option selected value="related">related</option>';
			} else {
				$additionlProductAttributes .= '<option value="related">related</option>';
			}
			if($seletedData == "upsell") {
				$additionlProductAttributes .= '<option selected value="upsell">upsell</option>';
			} else {
				$additionlProductAttributes .= '<option value="upsell">upsell</option>';
			}
			if($seletedData == "crosssell") {
				$additionlProductAttributes .= '<option selected value="crosssell">crosssell</option>';
			} else {
				$additionlProductAttributes .= '<option value="crosssell">crosssell</option>';
			}
			if($seletedData == "tier_prices") {
				$additionlProductAttributes .= '<option selected value="tier_prices">tier_prices</option>';
			} else {
				$additionlProductAttributes .= '<option value="tier_prices">tier_prices</option>';
			}
			if($seletedData == "store") {
				$additionlProductAttributes .= '<option selected value="store">store</option>';
			} else {
				$additionlProductAttributes .= '<option value="store">store</option>';
			}
			if($seletedData == "store_id") {
				$additionlProductAttributes .= '<option selected value="store_id">store_id</option>';
			} else {
				$additionlProductAttributes .= '<option value="store_id">store_id</option>';
			}
			if($seletedData == "prodtype") {
				$additionlProductAttributes .= '<option selected value="prodtype">prodtype</option>';
			} else {
				$additionlProductAttributes .= '<option value="prodtype">prodtype</option>';
			}
			if($seletedData == "websites") {
				$additionlProductAttributes .= '<option selected value="websites">websites</option>';
			} else {
				$additionlProductAttributes .= '<option value="websites">websites</option>';
			}
		foreach ($productAttributes as $productAttr) {
			if($seletedData == $productAttr->getAttributeCode()) {
				$optionAttr = "selected";
			} else {
				$optionAttr = "";
			}
			$additionlProductAttributes .= '<option '.$optionAttr.' value="'.$productAttr->getAttributeCode().'">'.$productAttr->getAttributeCode().'</option>';
		}
		return $additionlProductAttributes;
	}
	/**
     * Retrieve Product Attribute Sets
     *
     * @return html
     * @codeCoverageIgnore
     */
    public function getProductAttributeSets()
    {
		$additionlProductAttributeSets ='';
		foreach ($this->productAttributeSets->toOptionArray() as $productAttrSet) {
			$additionlProductAttributeSets .= '<option value="'.$productAttrSet["value"].'">'.$productAttrSet["label"].'</option>';
		}
		return $additionlProductAttributeSets;
	}
	/**
     * Render Select Value
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function renderValue($profileValue)
    {
		if($profileValue !="") {
			if($profileValue == "true") {
				return true;
			} else if($profileValue == "false") {
				return false;
			} else {	
				return $profileValue;
			}
		} else {
			return false;
		}
	}
	/**
     * Check Select Value Set Selected if saved
     *
     * @return array
     * @codeCoverageIgnore
     */
	public function checkFieldValue($value) {
	
		if($this->renderValue($value)) { 
			return "selected"; 
		}
		return '';
	}
	
	/**
     * Retrieve Import Profile Details
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getImportProfileData()
    {
		$data = $this->resource->getTableName('productimportexport_profiledata');
		$rs = $this->resource->getConnection()->query("SELECT * FROM ". $data." WHERE profile_type = 'import'");
		$rows = $rs->fetchAll();
		if(count($rows)){
			return $rows[0];
		} else {
			return array(
			'import_enclose'=> '"',
			'import_delimiter'=> ',',
			'root_catalog_id'=> '',
			'enable_default_magento_format'=> false,
			'import_attribute_value'=> false,
			'attribute_for_import_value'=> '',
			'ref_by_product_id'=> false,
			'create_products_only'=> false,
			'update_products_only'=> false,
			'import_images_by_url'=> false,
			'reimport_images'=> false,
			'deleteall_andreimport_images'=> false,
			'append_websites'=> false,
			'append_tier_prices'=> false,
			'append_categories'=> false,
			'append_grouped_products'=> false,
			'auto_create_categories'=> false,
			'import_fields'=> true,
			'import_fields_mapped'=> "",
			);
		}
	}
	/**
     * Retrieve Import Profile Details
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getExportProfileData()
    {
		$data = $this->resource->getTableName('productimportexport_profiledata');
		$rs = $this->resource->getConnection()->query("SELECT * FROM ". $data." WHERE profile_type = 'export'");
		$rows = $rs->fetchAll();
		if(count($rows)){
			return $rows[0];
		} else {
			return array(
				'export_delimiter'=> ',',
				'export_enclose'=> '"',
				'export_manual_file_name'=> 'export_products.csv',
				'export_fields'=> true,
				'export_fields_mapped'=> '',
				'apply_additional_filters'=> false,
				'filter_qty_from'=> '',
				'filter_qty_to'=> '',
				'filter_status'=> '',
				'product_id_from'=> '',
				'product_id_to'=> '',
				'export_grouped_position'=> false,
				'export_related_position'=> false,
				'export_crossell_position'=> false,
				'export_upsell_position'=> false,
				'export_category_paths'=> false,
				'export_full_image_paths'=> false,
				'export_multi_store'=> false,
			);
		}
	}
	/**
     * Retrieve Uploaded Files
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getUploadedFiles()
    {
		$fileuploadedindir = array();
		$data = $this->resource->getTableName('productimportexport_uploadedfiledata');
		$uploadedfiledata = $this->resource->getConnection()->query("SELECT * FROM ". $data);
		
		foreach($uploadedfiledata->fetchAll() as $uploaded_file){
			if(file_exists($uploaded_file['file_uploaded_path'].'/'.$uploaded_file['file_name'])) {
				$fileuploadedindir[]=$uploaded_file['file_name'];
			}
		 }
        return $fileuploadedindir;
    }
	/**
     * Retrieve Cron Schedule
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getCronSchedule()
    {
		$cron_schedule = $this->resource->getTableName('cron_schedule');
		return $this->resource->getConnection()->query("SELECT * FROM ". $cron_schedule ." WHERE job_code = 'export_products' OR job_code = 'import_products' ORDER BY created_at DESC LIMIT 10");
	}
	/**
     * Retrieve Cron Job Details
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getCronJobDetails()
    {
		return $this->resource->getConnection()->query("SELECT * FROM ". $this->resource->getTableName('productimportexport_cronjobdata') ." ORDER BY post_id ASC");
 	}
}
