<?php

/**
 * Copyright © 2015 CommerceExtensions. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CommerceExtensions\ProductImportExport\Model\Data\Import;

#use Magento\Framework\App\Filesystem\DirectoryList;
#use Magento\Framework\Filesystem;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ResourceConnection;

/**
 *  CSV Import Handler Simple Product
 */
 
class SimpleProduct {
		
	protected $ProductFactory;
	
    protected $_resource;
	
    protected $_skipFields  = ['image_exclude', 'store', 'prodtype'];
	
    public function __construct(
        ResourceConnection $resource,
		\Magento\Catalog\Model\ProductFactory $ProductFactory,
		\Magento\Catalog\Model\Product $Product,
		\Magento\Catalog\Model\ResourceModel\Product $productResourceModel,
		\Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $ProductLinkInterfaceFactory,
		\Magento\Catalog\Api\ProductRepositoryInterface $ProductRepositoryInterface,
		\Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterfaceFactory $ProductAttributeMediaGalleryEntryInterfaceFactory,
		\CommerceExtensions\ProductImportExport\Helper\Data $helper
    ) {
         // prevent admin store from loading
         $this->_resource = $resource;
		 $this->ProductFactory = $ProductFactory;
		 $this->Product = $Product;
    	 $this->productResourceModel = $productResourceModel;
		 $this->ProductLinkInterfaceFactory = $ProductLinkInterfaceFactory;
		 $this->ProductRepositoryInterface = $ProductRepositoryInterface;
		 $this->ProductAttributeMediaGalleryEntryInterfaceFactory = $ProductAttributeMediaGalleryEntryInterfaceFactory;
         $this->helper = $helper;
    }
	
	public function SimpleProductData($rowCount,$productID,$newProduct,$params,$ProcuctData,$ProductAttributeData,$ProductImageGallery,$ProductStockdata,$ProductSupperAttribute,$ProductCustomOption){
	
		$this->helper->rowCount = $rowCount;
		//UPDATE PRODUCT ONLY [START]
		$allowUpdateOnly = false;
		if($newProduct && $params['update_products_only'] == "true") {
			$allowUpdateOnly = true;
		} 
		//UPDATE PRODUCT ONLY [END]
		
		if ($allowUpdateOnly == false) {
			#$imagePath = "/import";
			
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
			#$this->helper->sendLog($this->helper->rowCount, 'store_id', 'STORE ID: ' . $productModel->getStoreId());
			//force attributes NULL if not set in csv FOR STORE VIEWS other then admin ONLY... w/o it leaves them unchecked
			if($ProductAttributeData['store_id'] != 0 && !$newProduct) {
				if(!isset($ProductAttributeData['name'])) { $ProductAttributeData['name'] = null; }
				if(!isset($ProductAttributeData['description'])) { $ProductAttributeData['description'] = null; }
				if(!isset($ProductAttributeData['short_description'])) { $ProductAttributeData['short_description'] = null; }
				if(!isset($ProductAttributeData['status'])) { $ProductAttributeData['status'] = null; }
				if(!isset($ProductAttributeData['tax_class_id'])) { $ProductAttributeData['tax_class_id'] = null; }
				if(!isset($ProductAttributeData['visibility'])) { $ProductAttributeData['visibility'] = null; }
				if(!isset($ProductAttributeData['is_returnable'])) { $ProductAttributeData['is_returnable'] = null; }
				if(!isset($ProductAttributeData['options_container'])) { $ProductAttributeData['options_container'] = null; }
				if(!isset($ProductAttributeData['url_key'])) { $ProductAttributeData['url_key'] = null; }
			}
			
			if($ProductAttributeData['store_id'] > 0 && $productID != ""){
				#$this->helper->sendLog($this->helper->rowCount, 'store_id', 'STORE ID UPDATE: ' . $ProductAttributeData['store_id'] . "PRODUCT ID: " . $productID);
				$this->SetDataToStoreView($productID, $ProductAttributeData);
			} else { 
			
			if(empty($ProductAttributeData['url_key'])) {
				unset($ProductAttributeData['url_key']);
				//$ProductAttributeData['url_key'] = null;
			} else {
				//this solve the error:  URL key for specified store already exists. 
				$urlrewrite = $this->helper->checkUrlKey($ProcuctData['store_id'], $ProductAttributeData['url_key']);
				if ($urlrewrite->getId()) {
					for ($addNumberUrlKey = 0; $addNumberUrlKey <= 10; $addNumberUrlKey++) {
						$addToKey = $addNumberUrlKey + 1;
						$newUrlKey = $ProductAttributeData['url_key'] . '-' . $addToKey;
						$urlrewriteCheck = $this->helper->checkUrlKey($ProcuctData['store_id'], $newUrlKey);
						if (!$urlrewriteCheck->getId()) { break; }
					}
					$ProductAttributeData['url_key'] = $newUrlKey;
					$ProductAttributeData['url_path'] = $newUrlKey;
				}
			}
			if(empty($ProductAttributeData['url_path'])) { unset($ProductAttributeData['url_path']); }
			if(isset($ProductAttributeData['product_id'])) { unset($ProductAttributeData['product_id']); }
			
			$productModel = $this->ProductFactory->create(['data' => $ProductAttributeData]);
				
			//get existing custom options and set them if we are not passing in any
			if(empty($ProductCustomOption)) {
				#$this->helper->sendLog($this->helper->rowCount, 'store_id', 'STORE ID UPDATE: ' . $ProductAttributeData['store_id'] . "PRODUCT ID: " . $productID);
				$productModel2 = $this->ProductFactory->create()->setStoreId($ProductAttributeData['store_id'])->load($productModel->getId());
				$customOptions = $productModel2->getOptions();
				$productModel->setCanSaveCustomOptions(true);
				$productModel->setOptions($customOptions); //added this in 2.2.3
				$productModel->setHasOptions(true); //added this in 2.2.3
				#$productModel->save();
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
							$entries =  [];
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
			
			//ADDED IMAGE LABELS - START
			/*
			$labelCounter = 0;
			$existingMediaGalleryEntries = $productModel->getMediaGalleryEntries();
			if(!empty($existingMediaGalleryEntries)) {
				foreach ($existingMediaGalleryEntries as $key => $entry) {
					if(in_array("image", $entry->getTypes())) {
						$entry->setLabel($ProductImageGallery['image_label']);
					} else if(in_array("small_image", $entry->getTypes())) {
						$entry->setLabel($ProductImageGallery['small_image_label']);
					} else if(in_array("thumbnail", $entry->getTypes())) {
						$entry->setLabel($ProductImageGallery['thumbnail_label']);
					} else if(in_array("swatch_image", $entry->getTypes())) {
						$entry->setLabel($ProductImageGallery['swatch_image_label']);
					} else if(!in_array("image", $entry->getTypes())) {
						if (!empty( $ProductImageGallery['gallery_label'])) {
							$galleryLabels = explode(';', $ProductImageGallery['gallery_label']);
							if(isset($galleryLabels[$labelCounter])) {
								$entry->setLabel($galleryLabels[$labelCounter]);
								$labelCounter++;
							}
						}
					}
				}
				$productModel->setStoreId($productModel->getStoreId());
				$productModel->setMediaGalleryEntries($existingMediaGalleryEntries);
			}
			*/
			//ADDED IMAGE LABELS - END
			
			if($ProductStockdata!=""){ $productModel->setStockData($ProductStockdata); }
			
			$relatedProductData = array();
			$upSellProductData = array();
			$crossSellProductData = array();
			
			if($ProductSupperAttribute['related']!=""){ $relatedProductData = $this->helper->AppendRelatedProduct($ProductSupperAttribute['related'],$ProcuctData['sku']);}
			if($ProductSupperAttribute['upsell']!=""){ $upSellProductData = $this->helper->AppendUpsellProduct($ProductSupperAttribute['upsell'],$ProcuctData['sku']);}
			if($ProductSupperAttribute['crosssell']!=""){ $crossSellProductData = $this->helper->AppendCrossSellProduct($ProductSupperAttribute['crosssell'],$ProcuctData['sku']);}
			
			if(!empty($relatedProductData) || !empty($upSellProductData) || !empty($crossSellProductData)) {
				$allProductLinks = array_merge($relatedProductData, $upSellProductData, $crossSellProductData);
				$productModel->setProductLinks($allProductLinks);
			}
		
			if($ProductSupperAttribute['tier_prices']!=""){ 
				$productModel->setTierPrice($ProductSupperAttribute['tier_prices']); 
			}
			
			try {
				$productModel->setIsMassupdate(true);
				//set custom options from csv
				if(!empty($ProductCustomOption)) {
					$productModel->setCanSaveCustomOptions(true);
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
					foreach ($ProductCustomOption as $arrayOption) {
							$option = $objectManager->create('\Magento\Catalog\Model\Product\Option')
													->setProductId($productModel->getId())
													->setStoreId($ProcuctData['store_id'])
													->addData($arrayOption);
							$productModel->addOption($option);//$option->save(); //removed this in 2.2.3
					}
					$productModel->setHasOptions(true); //added this in 2.2.3
					#$productModel->save();
				}
				$this->productResourceModel->save($productModel);
				if(!$productID) {
					$productID = $productModel->getEntityId();
				}
				/* For get Version Number */
				#$productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
				#$version = $productMetadata->getVersion();
				#if($version < '2.1.0'){ };
				if($params['create_products_only'] == "false"){
					if(isset($ProductAttributeData['price']) || isset($ProductAttributeData['special_price']) || isset($ProductAttributeData['cost'])) {
						$product2 = $this->Product->load($productID);
						if(isset($ProductAttributeData['price'])) { 
							$product2->setData('price', $ProductAttributeData['price']);
							$product2->getResource()->saveAttribute($product2, 'price');
						}
						if(isset($ProductAttributeData['special_price'])) { 
							$product2->setData('special_price', $ProductAttributeData['special_price']); 
							$product2->getResource()->saveAttribute($product2, 'special_price');
						}
						if(isset($ProductAttributeData['cost'])) { 
							$product2->setData('cost', $ProductAttributeData['cost']); 
							$product2->getResource()->saveAttribute($product2, 'cost');
						}
					}
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
			
			if(isset($ProductAttributeData['additional_attributes'])){
				$this->SetDataTosimpleProducts($ProcuctData, $ProductAttributeData['additional_attributes']);
			}
			
			} //CHECK FOR IF STORE VIEW OR NOT
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
        $defaultAttributes = ["price", "name", "description", "short_description", "meta_title", "meta_description", "meta_keyword", "tax_class_id", "url_key"];
        $userDefindedAttributes = $this->helper->getUserDefinedAttributes();
        $attributesArray = array_merge($defaultAttributes, $userDefindedAttributes);
        foreach ($cleanedData as $rowIndex => $dataRow) {
			if (in_array($rowIndex, $attributesArray)) {
				$product->setData($rowIndex, $dataRow);
				$this->productResourceModel->saveAttribute($product, $rowIndex);
			}
		}
		
	}
	public function SetDataTosimpleProducts($product, $ProductsFieldArray){
	    $Atdata = explode(',', $ProductsFieldArray);
	    #$Atdata = explode('^', $ProductsFieldArray);
		foreach($Atdata as $data){
		if(!empty($data) && $data !="")
			$pdata = explode('=', $data);
			if(isset($pdata[1])) {
				$AttributeCol = $this->Product->getResource()->getAttribute($pdata[0]);
 				if ($AttributeCol->usesSource()) {
					$OptionId = $AttributeCol->getSource()->getOptionId($pdata[1]);
				} else {
					$OptionId = $pdata[1];
				}
				$ProductId = $this->Product->getResource()->getIdBySku($product['sku']);
				$product = $this->Product->load($ProductId);
				$product->setData($pdata[0] , $OptionId);
				$product->setStoreId($product['store_id']);
				$product->getResource()->saveAttribute($product, $pdata[0]);
			}
		}
	
	}

}