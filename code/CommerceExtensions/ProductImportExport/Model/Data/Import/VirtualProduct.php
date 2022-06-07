<?php

/**
 * Copyright © 2019 CommerceExtensions. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CommerceExtensions\ProductImportExport\Model\Data\Import;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product;

/**
 *  CSV Import Handler Virtual Product
 */
 
class VirtualProduct{
		
	protected $ProductFactory;
	
    public function __construct(
		\Magento\Catalog\Model\ProductFactory $ProductFactory,
		\Magento\Catalog\Model\Product $Product,
		\Magento\Catalog\Model\ResourceModel\Product $productResourceModel,
		\Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $ProductLinkInterfaceFactory,
		\Magento\Catalog\Api\ProductRepositoryInterface $ProductRepositoryInterface,
		\Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterfaceFactory $ProductAttributeMediaGalleryEntryInterfaceFactory,
		\CommerceExtensions\ProductImportExport\Helper\Data $helper
    ) {
         // prevent admin store from loading
		 $this->ProductFactory = $ProductFactory;
		 $this->Product = $Product;
    	 $this->productResourceModel = $productResourceModel;
		 $this->ProductLinkInterfaceFactory = $ProductLinkInterfaceFactory;
		 $this->ProductRepositoryInterface = $ProductRepositoryInterface;
		 $this->ProductAttributeMediaGalleryEntryInterfaceFactory = $ProductAttributeMediaGalleryEntryInterfaceFactory;
         $this->helper = $helper;
    }
	
	public function VirtualProductData($rowCount,$productID,$newProduct,$params,$ProcuctData,$ProductAttributeData,$ProductImageGallery,$ProductStockdata,$ProductSupperAttribute){
	
	
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
					$skipImageOnMatch = false;
					if($ImageFile != "no_selection") {
						$existingEntryIds = [];
						if(!$newProduct) {
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
					} else {
						foreach( $imageColumns as $mediaAttribute ) {
							$productModel->setData($mediaAttribute, 'no_selection');
						}
					}
				}
			}
			
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
				$this->productResourceModel->save($productModel);
			}
			catch (\Exception $e) { 
				if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
					$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, "ERROR: " . $e->getMessage());
					$this->helper->writeToCsv($cronLogErrors);	
				} else {
					throw new \Magento\Framework\Exception\LocalizedException(__('SKU: '.$ProcuctData['sku'].' ERROR : '. $e->getMessage()));
				}
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
        foreach ($cleanedData as $rowIndex => $dataRow) {
			#$this->helper->sendLog($this->helper->rowCount, 'store_id', 'rowIndex: ' . $rowIndex . "dataRow: " . $dataRow);
			if($rowIndex == "price" || $rowIndex == "name" || $rowIndex == "description" || $rowIndex == "short_description" || $rowIndex == "meta_title" || $rowIndex == "meta_description" || $rowIndex == "meta_keyword" || $rowIndex == "tax_class_id" || $rowIndex == "url_key") {
				#$this->helper->sendLog($this->helper->rowCount, 'store_id', 'rowIndex: ' . $rowIndex . " dataRow: " . $dataRow);
				$product->setData($rowIndex, $dataRow);
				$this->productResourceModel->saveAttribute($product, $rowIndex);
			}
		}
		//must have these set otherwise they get unchecked
		#$product->setData('url_key', null);
		#$this->productResourceModel->saveAttribute($product, 'url_key');
		
	}
}