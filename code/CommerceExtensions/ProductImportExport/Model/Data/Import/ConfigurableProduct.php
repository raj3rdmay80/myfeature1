<?php

/**
 * Copyright © 2018 CommerceExtensions. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CommerceExtensions\ProductImportExport\Model\Data\Import;

//use Magento\Framework\App\Filesystem\DirectoryList;
//use Magento\Framework\Filesystem;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

/**
 *  CSV Import Handler Configurable Product
 */
 
class ConfigurableProduct{

	protected $ProductFactory;
	
    public function __construct(
		\Magento\Catalog\Model\ProductFactory $ProductFactory,
		\Magento\Catalog\Model\Product $Product,
		\Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $ProductLinkInterfaceFactory,
		\Magento\Catalog\Api\ProductRepositoryInterface $ProductRepositoryInterface,
		\Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterfaceFactory $ProductAttributeMediaGalleryEntryInterfaceFactory,
		\Magento\Catalog\Model\ResourceModel\Product $productResourceModel,
		\Magento\Eav\Model\ResourceModel\Entity\Attribute $Attribute,
		\Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $ConfigurableProduct,
		\Magento\Catalog\Model\ResourceModel\Product\Collection $ProductCollection,
		\CommerceExtensions\ProductImportExport\Helper\Data $helper
		
    ) {
         // prevent admin store from loading
		 $this->ProductFactory = $ProductFactory;
		 //$this->_filesystem = $filesystem;
		 $this->Product = $Product;
		 $this->ProductLinkInterfaceFactory = $ProductLinkInterfaceFactory;
		 $this->ProductRepositoryInterface = $ProductRepositoryInterface;
		 $this->ProductAttributeMediaGalleryEntryInterfaceFactory = $ProductAttributeMediaGalleryEntryInterfaceFactory;
    	 $this->productResourceModel = $productResourceModel;
		 $this->Attribute = $Attribute;
		 $this->ConfigurableProduct = $ConfigurableProduct;
		 $this->ProductCollection = $ProductCollection;
         $this->helper = $helper;
    }
	
	public function ConfigurableProductData($rowCount,$productID,$newProduct,$params,$ProcuctData,$ProductAttributeData,$ProductImageGallery,$ProductStockdata,$ProductSupperAttribute,$ProductCustomOption){
	
	$this->helper->rowCount = $rowCount;
	//UPDATE PRODUCT ONLY [START]
	$allowUpdateOnly = false;
	
	if($newProduct && $params['update_products_only'] == "true") {
		$allowUpdateOnly = true;
	}
	//UPDATE PRODUCT ONLY [END]
	
	if ($allowUpdateOnly == false) {
		
		#$imagePath = "/import";
		
		if(empty($ProductAttributeData['url_key'])) {
			unset($ProductAttributeData['url_key']);
		} else {
			//this solve the error:  URL key for specified store already exists. 
			$urlrewrite = $this->helper->checkUrlKey($ProcuctData['store_id'], $ProductAttributeData['url_key']);
			if ($urlrewrite->getId()) {
				for ($addNumberUrlKey = 0; $addNumberUrlKey <= 10; $addNumberUrlKey++) {
					$addToKey = $addNumberUrlKey + 1;
					$newUrlKey = $ProductAttributeData['url_key'] . '-' . $addToKey;
					$urlrewriteCheck = $this->helper->checkUrlKey($ProcuctData['store_id'], $newUrlKey);
					if (!$urlrewriteCheck->getId()) {
						break;
					}
				}
				$ProductAttributeData['url_key'] = $newUrlKey;
				$ProductAttributeData['url_path'] = $newUrlKey;
			}
		}
		if(empty($ProductAttributeData['url_path'])) {
			unset($ProductAttributeData['url_path']);
		}
		if(isset($ProductAttributeData['product_id'])) {
			unset($ProductAttributeData['product_id']);
		}
		
		#if(isset($ProcuctData['prodtype'])) { $SetProductData->setTypeId($ProcuctData['prodtype']); }
		
		$ProductAttributeData['entity_id'] = $productID;
		$ProductAttributeData['sku'] = $ProcuctData['sku'];
		$ProductAttributeData['store_id'] = $ProcuctData['store_id'];
		if(isset($ProcuctData['websites'])) { $ProductAttributeData['website_ids'] = $ProcuctData['websites']; }
		if(isset($ProcuctData['attribute_set'])) { $ProductAttributeData['attribute_set_id'] = $ProcuctData['attribute_set']; }
		if(isset($ProcuctData['prodtype'])) { $ProductAttributeData['type_id'] = $ProcuctData['prodtype']; }
		if(isset($ProcuctData['category_ids'])) { 
			if($ProcuctData['category_ids'] == "remove") { 
				$ProductAttributeData['category_ids'] = array(); 
			} else if($ProcuctData['category_ids'] != "") { 
				$ProductAttributeData['category_ids'] = $ProcuctData['category_ids'];
			}
		}
		#$SetProductData->setData($ProductAttributeData);
		$productModel = $this->ProductFactory->create(['data' => $ProductAttributeData]);
		
		//get existing custom options and set them if we are not passing in any
		if(empty($ProductCustomOption)) {
			#$productModel2 = $this->ProductFactory->create()->setStoreId(0)->load($productModel->getId());
			$productModel2 = $this->ProductFactory->create()->setStoreId($ProductAttributeData['store_id'])->load($productModel->getId());
			$customOptions = $productModel2->getOptions();
			$productModel->setOptions($customOptions); //added this in 2.2.3
			$productModel->setHasOptions(true); //added this in 2.2.3
			$productModel->setCanSaveCustomOptions(true);
			#$productModel->save();
		} else {
			$customOptions = [];
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			$customOptionFactory = $objectManager->create('Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory');
			foreach ($ProductCustomOption as $arrayOption) {
				$customOption = $customOptionFactory->create(['data' => $arrayOption]);
				$customOption->setProductSku($productModel->getSku());
				$customOptions[] = $customOption;
			}
			$productModel->setOptions($customOptions);
			$productModel->setHasOptions(true); //added this in 2.2.3
			$productModel->setCanSaveCustomOptions(true);
			#$productModel->setOptions($customOptions)->save();
		
		}
		
		if($newProduct || $params['reimport_images'] == "true") { 
				
		//media images
		$_productImages = array(
			'media_gallery'       => ($ProductImageGallery['gallery']!="") ? $ProductImageGallery['gallery'] : 'no_selection',
			'image'       => ($ProductImageGallery['image']!="") ? $ProductImageGallery['image'] : 'no_selection',
			'small_image'       => ($ProductImageGallery['small_image']!="") ? $ProductImageGallery['small_image'] : 'no_selection',
			'thumbnail'       => ($ProductImageGallery['thumbnail']!="") ? $ProductImageGallery['thumbnail'] : 'no_selection',
			'swatch_image'       => ($ProductImageGallery['swatch_image']!="") ? $ProductImageGallery['swatch_image'] : 'no_selection'
	
		);
		
		//create array of images with duplicates combined
		$labelCounter = 0;
		$imageArray = array();
		foreach ($_productImages as $columnName => $imageName) {
			$imageArray = $this->helper->addImage($imageName, $columnName, $imageArray);
		}
		
		foreach ($imageArray as $ImageFile => $imageColumns) {
		#foreach ($_productImages as $columnName => $ImageFile) {
			$skipImageOnMatch = false;
			if($ImageFile != "no_selection") {
				/*
				if($this->helper->checkIfImageIsExcluded($ProductAttributeData, $imageColumns)) { 
					$imageData = $productModel->addImageToMediaGallery($imagePath . $ImageFile, $imageColumns, false, true); //last false to true exclude / hide image
				} else {
					$imageData = $productModel->addImageToMediaGallery($imagePath . $ImageFile, $imageColumns, false, false);
				}
				*/
				$existingEntryIds = [];
				if(!$newProduct) {
					/*
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
					$productRepository = $objectManager->get('Magento\Catalog\Api\ProductRepositoryInterface');
					$product = $productRepository->get($ProductAttributeData['sku']);
					*/
					$product = $this->ProductRepositoryInterface->get($ProductAttributeData['sku']);
					$entries = $product->getMediaGalleryEntries();
				} else {
					$entries = [];
				}
				if(is_array($entries)) {
					foreach ($entries as $mediaEntry) {
						if (basename($mediaEntry->getFile()) === basename($ImageFile)) {
							$this->helper->sendLog($this->helper->rowCount, implode(" / " , $imageColumns), 'Image Already Exists: ' . basename($mediaEntry->getFile()));
							#$mediaEntry->unsData('file');
							$skipImageOnMatch = true;
							break;
						}
					}
				}
				//above code is for update of a product.. the create below works fine for new only
				if(!$skipImageOnMatch) {
					#if (!isset($mediaEntry)) {
						$mediaEntry = $this->ProductAttributeMediaGalleryEntryInterfaceFactory->create();
					#}
					#$content = $this->getContentObject($ImageFile);
					$content = $this->helper->getContentObject($ImageFile);
					$mediaEntry->setContent($content);
					$mediaEntry->setMediaType('image');
					$mediaEntry->setPosition('0');
					$mediaEntry->setFile($ImageFile);
					$this->helper->sendLog($this->helper->rowCount, implode(" / " , $imageColumns), 'Importing New Image: ' . $ImageFile);						
					$mediaEntry->setTypes($imageColumns);
					
					if($this->helper->checkIfImageIsExcluded($ProductAttributeData, $imageColumns)) { 
						$mediaEntry->setDisabled(true);
					} else {
						$mediaEntry->setDisabled(false);
					}
					#if($columnName == "image" && $_productImages['image'] !="no_selection") {
					if(in_array("image", $imageColumns)) {
						#$productModel->setImage($_productImages['image']);
						$productModel->setImage($ImageFile);
						#$this->helper->sendLog($this->helper->rowCount, '', 'setImage()' . $ImageFile);
						if(isset($ProductImageGallery['image_label'])) {
							$mediaEntry->setLabel($ProductImageGallery['image_label']);
						}
					}
					#if($columnName == "small_image" && $_productImages['small_image'] !="no_selection") {
					if(in_array("small_image", $imageColumns)) {
						#$productModel->setSmallImage($_productImages['small_image']);
						$productModel->setSmallImage($ImageFile);
						#$this->helper->sendLog($this->helper->rowCount, '', 'setSmallImage()' . $ImageFile);
						if(isset($ProductImageGallery['small_image_label'])) {
							$mediaEntry->setLabel($ProductImageGallery['small_image_label']);
						}
					}
					#if($columnName == "thumbnail" && $_productImages['thumbnail'] !="no_selection") {
					if(in_array("thumbnail", $imageColumns)) {
						#$productModel->setThumbnail($_productImages['thumbnail']);
						$productModel->setThumbnail($ImageFile);
						#$this->helper->sendLog($this->helper->rowCount, '', 'setThumbnail()' . $ImageFile);
						if(isset($ProductImageGallery['thumbnail_label'])) {
							$mediaEntry->setLabel($ProductImageGallery['thumbnail_label']);
						}
					}
					#if($columnName == "swatch_image" && $_productImages['swatch_image'] !="no_selection") {
					if(in_array("swatch_image", $imageColumns)) {
						#$productModel->setSwatchImage($_productImages['swatch_image']);
						$productModel->setSwatchImage($ImageFile);
						if(isset($ProductImageGallery['swatch_image_label'])) {
							$mediaEntry->setLabel($ProductImageGallery['swatch_image_label']);
						}
					}	
					if(in_array("media_gallery", $imageColumns)) {
						if (!empty( $ProductImageGallery['gallery_label'])) {
							$galleryLabels = explode(';', $ProductImageGallery['gallery_label']);
							if(isset($galleryLabels[$labelCounter])) {
								$mediaEntry->setLabel($galleryLabels[$labelCounter]);
								$mediaEntry->setPosition($labelCounter); //set position on gallery image
								$labelCounter++; // this is here 
							}
						}
					}
					$entries[] = $mediaEntry;
					$productModel->setStoreId($productModel->getStoreId());
					$productModel->setMediaGalleryEntries($entries);	
					$productModel->save();	
				} else {
					if(in_array("media_gallery", $imageColumns)) {
						if (!empty( $ProductImageGallery['gallery_label'])) {
							$galleryLabels = explode(';', $ProductImageGallery['gallery_label']);
							if(isset($galleryLabels[$labelCounter])) {
								$labelCounter++; 
								// this is here so if we are importing only 1 new gallery image and there is existing ones. 
								// that we only import the label for that gallery image
							}
						}
					}
				}
				#$productModel->setMediaGalleryEntries($entries);
				#$productModel->setData($mediaAttribute, 'no_selection');
				#$productRepository->save($product);									
			} else {
				foreach( $imageColumns as $mediaAttribute ) {
					$productModel->setData($mediaAttribute, 'no_selection');
				}
			}
		}
	}
			
		$productModel->setCanSaveConfigurableAttributes(true);
		$productModel->setCanSaveCustomOptions(true);
		$productModel->setTypeId("configurable");
		$cProductTypeInstance = $productModel->getTypeInstance();
		
		if(isset($ProductAttributeData['config_attributes'])) {
			$attribute_ids = $this->getConfigAttributesId($ProductAttributeData['config_attributes']);
			if(is_array($attribute_ids)) {
				$cProductTypeInstance->setUsedProductAttributeIds($attribute_ids,$productModel);
				$attributes_array = $cProductTypeInstance->getConfigurableAttributesAsArray($productModel);
				
				foreach($attributes_array as $key => $attribute_array) 
				{
					$attributes_array[$key]['use_default'] = 1;
					$attributes_array[$key]['position'] = 0;
			
					if (isset($attribute_array['frontend_label'])) { 
						$attributes_array[$key]['label'] = $attribute_array['frontend_label']; 
					} else { 
						$attributes_array[$key]['label'] = $attribute_array['attribute_code']; 
					}
				}
				// Add it back to the configurable product..
				$productModel->setConfigurableAttributesData($attributes_array);	
			}
		}
		
		if($ProductStockdata!=""){ $productModel->setStockData($ProductStockdata); }
		
		$relatedProductData = array();
		$upSellProductData = array();
		$crossSellProductData = array();
		
		if($ProductSupperAttribute['related']!=""){ $relatedProductData = $this->helper->AppendRelatedProduct($ProductSupperAttribute['related'] ,$ProcuctData['sku']); }
		if($ProductSupperAttribute['upsell']!=""){ $upSellProductData = $this->helper->AppendUpsellProduct($ProductSupperAttribute['upsell'] ,$ProcuctData['sku']); }
		if($ProductSupperAttribute['crosssell']!=""){ $crossSellProductData = $this->helper->AppendCrossSellProduct($ProductSupperAttribute['crosssell'] , $ProcuctData['sku']); }
		
		if(!empty($relatedProductData) || !empty($upSellProductData) || !empty($crossSellProductData)) {
			$allProductLinks = array_merge($relatedProductData, $upSellProductData, $crossSellProductData);
			$productModel->setProductLinks($allProductLinks);
		}
		
		try {
			#$SetProductData->save();
			$this->productResourceModel->save($productModel);
			if(!$productID) {
				$productID = $productModel->getEntityId();
			}
		}
		catch (\Exception $e) {
			if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
				$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, "ERROR: " . $e->getMessage());
				$this->helper->writeToCsv($cronLogErrors);	
			} else {
				throw new \Magento\Framework\Exception\LocalizedException(__('SKU: '.$ProcuctData['sku'].' ERROR : '. $e->getMessage()));
			}
		}
		
		if(!empty($ProductCustomOption)) {
			/*
			$customOptions = [];
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			$customOptionFactory = $objectManager->create('Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory');
			$productModel->setCanSaveCustomOptions(true);
			foreach ($ProductCustomOption as $arrayOption) {
				$customOption = $customOptionFactory->create(['data' => $arrayOption]);
				$customOption->setProductSku($productModel->getSku());
				$customOptions[] = $customOption;
			}
			$productModel->setOptions($customOptions)->save();
			*/
			#$SetProductData->setProductOptions($ProductCustomOption);
			#$SetProductData->setHasOptions(true); 
			#$SetProductData->setCanSaveCustomOptions(true);
			#$SetProductData->save(); 
			#print_r($ProductCustomOption);
			/*
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			foreach ($ProductCustomOption as $arrayOption) {
					$option = $objectManager->create('\Magento\Catalog\Model\Product\Option')
											->setProductId($SetProductData->getId())
											->setStoreId($ProcuctData['store_id'])
											->addData($arrayOption);
					$option->save();
					$SetProductData->addOption($option);
			}
			$SetProductData->save();
			*/
		} 
		if(isset($ProductAttributeData['additional_attributes'])){
			#$this->SetDataTosimpleProducts($ProductAttributeData['additional_attributes']);
		}
		$ConfigurableId = $ProcuctData['sku'];
		$this->SimpleAssociatedWithConfigureable($productModel, $ProductSupperAttribute['associated'], $ConfigurableId);
		
		if($ProductAttributeData['store_id'] != 0 && $productID != ""){
			#$this->helper->sendLog($this->helper->rowCount, 'store_id', 'STORE ID UPDATE: ' . $ProductAttributeData['store_id'] . "PRODUCT ID: " . $productID);
			$this->SetDataToStoreView($productID, $ProductAttributeData);
		}
	
	  }//END UPDATE ONLY CHECK
	  return $this->helper->msgtoreturn;
	}
	
	public function SetDataToStoreView($productID, $ProductAttributeData){
		
		$product = $this->ProductFactory->create()->reset();
		$this->productResourceModel->load($product, $productID);
		$product->setStoreId($ProductAttributeData['store_id']);
		if(isset($ProductAttributeData['price'])) { $product->setPrice($ProductAttributeData['price']); }
		#print_r(array_filter($ProductAttributeData));
		unset($ProductAttributeData['websites']);
		unset($ProductAttributeData['website_ids']);
		unset($ProductAttributeData['store']);
		unset($ProductAttributeData['prodtype']);
		unset($ProductAttributeData['sku']);
		unset($ProductAttributeData['store_id']);
		unset($ProductAttributeData['entity_id']);
		unset($ProductAttributeData['type_id']);
		$cleanedData = array_filter($ProductAttributeData);
		#print_r($cleanedData);
        foreach ($cleanedData as $rowIndex => $dataRow) {
			#$this->helper->sendLog($this->helper->rowCount, 'store_id', 'rowIndex: ' . $rowIndex . "dataRow: " . $dataRow);
			if($rowIndex == "price" || $rowIndex == "name" || $rowIndex == "description" || $rowIndex == "short_description" || $rowIndex == "meta_title" || $rowIndex == "meta_description") {
				#$this->helper->sendLog($this->helper->rowCount, 'store_id', 'rowIndex: ' . $rowIndex . " dataRow: " . $dataRow);
				$product->setData($rowIndex, $dataRow);
				$this->productResourceModel->saveAttribute($product, $rowIndex);
			}
		}
		
		#$this->productResourceModel->saveAttribute($product, 'price');
		
	}
	
	public function SetDataTosimpleProducts($ProductsFieldArray){
	    $Atdata = explode(',', $ProductsFieldArray);
		foreach($Atdata as $data){
		if(!empty($data) && $data !="")
			$pdata = explode('=', $data);
			if(isset($pdata[1])) {
				$AttributeCol = $this->Product->getResource()->getAttribute($pdata[1]);
				$OptionId = $AttributeCol->getSource()->getOptionId($pdata[2]);
				$ProductId = $this->Product->getResource()->getIdBySku($pdata[0]);
				$product = $this->Product->load($ProductId);
				$product->setData($pdata[1] , $OptionId);
				$product->getResource()->saveAttribute($product, $pdata[1]);
			}
		}
	
	}
	
	public function SimpleAssociatedWithConfigureable($SetProductData, $childProduct, $configurableProduct){
		if($childProduct!="") {
			$cpId = $this->Product->getResource()->getIdBySku($configurableProduct);
			$Products_sku = explode(',',$childProduct);
			#$Products_sku = explode('|',$childProduct);
			$ProductId = array();
			foreach($Products_sku as $sku){
				if($sku){
					if(!$this->Product->getResource()->getIdBySku($sku)) {
						//throw new \Magento\Framework\Exception\LocalizedException(__('The Following sku: "' . $sku. '" does NOT exist and cannot be used to create a configurable product. However you do have it listed as a sku in your "associated" column'));
						$this->helper->sendLog($this->helper->rowCount,'associated','The Following sku: "' . $sku. '" does NOT exist and cannot be used to create a configurable product. However you do have it listed as a sku in your "associated" column');
					} else {
						$ProductId[] = $this->Product->getResource()->getIdBySku($sku);
						$ProductId = array_unique($ProductId); //remove any duplicates if customer puts same sku twice in assoicated field
					}
				}
			}
			$this->ConfigurableProduct->saveProducts($SetProductData,$ProductId);
		} else {
			$this->helper->sendLog($this->helper->rowCount,'associated',"The column is empty and must contain the sku's in a comma delimited from that you want to associate to a configurable product");		
		}
	}
	
	public function getConfigAttributesId($AttributesCode){
		if($AttributesCode!="") {
			$Codes = explode(',', $AttributesCode);
			#$Codes = explode('|', $AttributesCode);
			$AttributeId = array();
			foreach($Codes as $Code){ 
				if($this->Attribute->getIdByCode('catalog_product',trim($Code))) {
					$AttributeId[] = $this->Attribute->getIdByCode('catalog_product',trim($Code)); //getIdByCode($entityType, $code)
				} else {
					$this->helper->sendLog($this->helper->rowCount,'config_attributes','The column contains the following attribute "'.$Code.'" and it does NOT exist in the install');
				}
			}	
			return $AttributeId;
		} else {
			//throw new \Magento\Framework\Exception\LocalizedException(__('The column "config_attributes" is empty and should contain the attribute names you want to use to create a configurable product e.g "color,size"'));
			$this->helper->sendLog($this->helper->rowCount,'config_attributes','The column is empty and should contain the attribute names you want to use to create a configurable product e.g "color,size"');
		}
	}
}